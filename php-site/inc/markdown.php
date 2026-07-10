<?php
/**
 * Compact Markdown → HTML converter covering the features used by the blog:
 * ATX headings, bold/italic, inline code, links, images, ordered/unordered
 * lists, blockquotes, fenced code blocks, GFM tables, horizontal rules and
 * paragraphs. Content is authored only by the site owner in /admin.
 */
function markdown(string $md): string {
    $md = str_replace(["\r\n", "\r"], "\n", $md);
    $lines = explode("\n", $md);
    $html = [];
    $n = count($lines);
    $i = 0;

    $inline = function (string $t): string {
        $t = htmlspecialchars($t, ENT_QUOTES, 'UTF-8');
        // inline code
        $t = preg_replace_callback('/`([^`]+)`/', fn($m) => '<code>' . $m[1] . '</code>', $t);
        // images ![alt](src)
        $t = preg_replace('/!\[([^\]]*)\]\(([^)\s]+)\)/', '<img src="$2" alt="$1" loading="lazy" />', $t);
        // links [text](href)
        $t = preg_replace('/\[([^\]]+)\]\(([^)\s]+)\)/', '<a href="$2">$1</a>', $t);
        // bold then italic
        $t = preg_replace('/\*\*([^*]+)\*\*/', '<strong>$1</strong>', $t);
        $t = preg_replace('/(?<!\*)\*([^*]+)\*(?!\*)/', '<em>$1</em>', $t);
        // strikethrough
        $t = preg_replace('/~~([^~]+)~~/', '<del>$1</del>', $t);
        return $t;
    };

    while ($i < $n) {
        $line = $lines[$i];
        $trim = trim($line);

        // blank
        if ($trim === '') { $i++; continue; }

        // fenced code
        if (preg_match('/^```/', $trim)) {
            $i++; $buf = [];
            while ($i < $n && !preg_match('/^```/', trim($lines[$i]))) { $buf[] = $lines[$i]; $i++; }
            $i++; // closing fence
            $html[] = '<pre><code>' . htmlspecialchars(implode("\n", $buf), ENT_QUOTES, 'UTF-8') . '</code></pre>';
            continue;
        }

        // horizontal rule
        if (preg_match('/^(\*\s*){3,}$|^(-\s*){3,}$|^(_\s*){3,}$/', $trim)) { $html[] = '<hr />'; $i++; continue; }

        // heading
        if (preg_match('/^(#{1,6})\s+(.*)$/', $trim, $m)) {
            $lvl = strlen($m[1]);
            $html[] = "<h$lvl>" . $inline(trim($m[2])) . "</h$lvl>";
            $i++; continue;
        }

        // table (GFM): header row then |---| separator
        if (strpos($trim, '|') !== false && $i + 1 < $n && preg_match('/^\s*\|?[\s:-]*-[\s:|-]*\|?\s*$/', $lines[$i + 1])) {
            $split = function ($row) {
                $row = trim($row);
                $row = preg_replace('/^\||\|$/', '', $row);
                return array_map('trim', explode('|', $row));
            };
            $headers = $split($lines[$i]);
            $i += 2;
            $rows = [];
            while ($i < $n && strpos($lines[$i], '|') !== false && trim($lines[$i]) !== '') {
                $rows[] = $split($lines[$i]); $i++;
            }
            $t = '<table><thead><tr>';
            foreach ($headers as $h) $t .= '<th>' . $inline($h) . '</th>';
            $t .= '</tr></thead><tbody>';
            foreach ($rows as $r) {
                $t .= '<tr>';
                foreach ($headers as $ci => $_) $t .= '<td>' . $inline($r[$ci] ?? '') . '</td>';
                $t .= '</tr>';
            }
            $t .= '</tbody></table>';
            $html[] = $t;
            continue;
        }

        // blockquote
        if (preg_match('/^>\s?/', $trim)) {
            $buf = [];
            while ($i < $n && preg_match('/^>\s?(.*)$/', trim($lines[$i]), $m)) { $buf[] = $m[1]; $i++; }
            $html[] = '<blockquote>' . $inline(implode(' ', $buf)) . '</blockquote>';
            continue;
        }

        // unordered list
        if (preg_match('/^[-*+]\s+/', $trim)) {
            $buf = [];
            while ($i < $n && preg_match('/^[-*+]\s+(.*)$/', trim($lines[$i]), $m)) { $buf[] = '<li>' . $inline($m[1]) . '</li>'; $i++; }
            $html[] = '<ul>' . implode('', $buf) . '</ul>';
            continue;
        }

        // ordered list
        if (preg_match('/^\d+\.\s+/', $trim)) {
            $buf = [];
            while ($i < $n && preg_match('/^\d+\.\s+(.*)$/', trim($lines[$i]), $m)) { $buf[] = '<li>' . $inline($m[1]) . '</li>'; $i++; }
            $html[] = '<ol>' . implode('', $buf) . '</ol>';
            continue;
        }

        // paragraph (gather until blank)
        $buf = [];
        while ($i < $n && trim($lines[$i]) !== ''
               && !preg_match('/^(#{1,6}\s|```|>|\s*[-*+]\s|\d+\.\s)/', trim($lines[$i]))) {
            $buf[] = trim($lines[$i]); $i++;
        }
        if ($buf) $html[] = '<p>' . $inline(implode(' ', $buf)) . '</p>';
    }

    return implode("\n", $html);
}
