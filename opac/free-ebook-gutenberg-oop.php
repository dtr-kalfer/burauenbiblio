<?php
		/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
		 * See the file COPYRIGHT.html for more details. --F.Tumulak
		 */
require_once("../shared/common.php");
require_once(__DIR__ . "/../classes/GutendexClient.php");

$tab = 'opac';
$nav = "free-ebook-gutenberg-oop.php";
Page::header(['nav' => $tab.'/'.$nav, 'title' => '']);

$client = new GutendexClient();

$query = $_GET['q'] ?? '';
$books = $query ? $client->search($query) : [];
?>
<style>
/* (same CSS as before) */
.container {
    max-width: 900px;        /* desktop cap */
    width: 98%;
    margin: auto;
    background: bisque;
}

#content { background:bisque; }
form {
    margin-bottom: 2rem;
    display: flex;
    gap: 0.5rem;
    justify-content: center;
}

input[type=text] {
    flex: 1;
    min-width: 200px;
    padding: 0.5rem;
    font-size: 1rem;
    text-align: center;
		border-radius: 8px;
}

button {
    padding: 0.5rem 2rem;
    font-size: 1rem;
    border: none;
    border-radius: 6px;
    background: #0078d7;
    color: white;
    cursor: pointer;
    white-space: nowrap;
}

.card { background:bisque; border-radius:8px; box-shadow:0 2px 5px rgba(0,0,0,0.1); padding:0.5rem; margin-bottom:0.5rem; }
.title { font-size:1rem; font-weight:bold; background:#eee; color:black; padding:5px; }
.author { color:#222; font-weight:bold; margin-bottom:0.5rem; padding:5px; }
.formats { display:flex; gap:0.5rem; flex-wrap:wrap; }
.format-btn { background:#eee; border-radius:4px; padding:0.2rem 0.6rem; text-decoration:none; color:#333; font-size:0.8rem; transition:background 0.2s; }
.format-btn:hover { background:#0078d7; color:white; }
.note { font-size:0.85rem; color:#999; margin-top:0.5rem; }
h4 { font-size:1.3rem; }

/* ------------------------------- */
/* Tablet scaling */
@media screen and (max-width: 600px) {

    form {
        flex-direction: column;
        align-items: center;
				gap: 0.75rem;
    }

    input[type=text],
    button {
        width: 95%;
    }

    button {
        padding: 0.6rem;
    }
}


/* Desktop scaling (Your existing logic) */
@media screen and (min-width: 800px) {
    html { font-size: calc(0.5 * (1em + 1vw)); }
}

/* Responsive adjustment for small screens */
@media screen and (max-width: 500px) {
	html { font-size: calc(1.0 * (0.8em + 0.5vw)); }

}
</style>

<h4>📚 Gutendex eBook Search</h4>
<h3>Project Gutenberg is a library of over 75,000 Free Public Domain Ebooks 📖📚🕮📕.</h3>

<section class="container">
<form method="get">
    <input id="inputgutendex" type="text" name="q" value="<?= htmlspecialchars($query) ?>" placeholder="Search books or authors...">
    <button type="submit">Search</button>
</form>

<?php if ($query && empty($books)): ?>
    <p>❌ No results found for <strong><?= htmlspecialchars($query) ?></strong>.</p>
<?php endif; ?>

<?php foreach ($books as $book): ?>
    <?php
        $title = htmlspecialchars($book['title']);
        $authors = array_map(fn($a) => $a['name'], $book['authors'] ?? []);
        $authorStr = htmlspecialchars(implode(', ', $authors) ?: 'n.a.');
        $formats = $book['formats'] ?? [];
        $id = $book['id'];
    ?>

    <div class="card">
        <div class="title"><?= $title ?></div>
        <div class="author">Author: <?= $authorStr ?></div>

        <div class="formats">
            <?php
                $hasDownload = false;
                if (!empty($formats['application/epub+zip'])) {
                    echo "<a class='format-btn' href='download-oop.php?id={$id}'>📘 EPUB</a>";
                    $hasDownload = true;
                }
                if (!empty($formats['text/plain; charset=utf-8']) || !empty($formats['text/plain; charset=us-ascii'])) {
                    echo "<a class='format-btn' href='download-oop.php?id={$id}&type=txt'>📄 TXT</a>";
                    $hasDownload = true;
                }
                if (!empty($formats['application/x-mobipocket-ebook'])) {
                    echo "<a class='format-btn' href='download-oop.php?id={$id}&type=mobi'>📱 MOBI</a>";
                    $hasDownload = true;
                }
                if (!$hasDownload) echo "<span class='note'>No downloadable formats available</span>";
            ?>
        </div>
    </div>
<?php endforeach; ?>
</section>

<p style="font-size:0.8rem; color:#555; text-align:center;">
eBook metadata provided by <a href="https://www.gutenberg.org/" target="_blank">Project Gutenberg</a>
via the <a href="https://gutendex.com/" target="_blank">Gutendex API</a> © Gareth Johnson (MIT License).
</p>
<p style="font-size:0.8rem; color:#555; text-align:center;">Public Domain — Free to read and share. 📖</p>

<?php require_once(REL(__FILE__, '../shared/footer.php')); ?>
