<?php include "includes/header.php"; ?>

<div id="filter-bar">
    <div id="genre-toggle-btn">
        <i class="fa-solid fa-bars"></i>
    </div>

    <form id="search-bar">
        <input id="search-input" type="text" data-lang="search.placeholder">
        <button id="search-button" type="button" data-lang="search.button"></button>
    </form>
</div>

<main>
    <section id="genre-list">

    </section>

    <div id="game-grid">
        <section id="favor-game-grid">
            <h2 data-lang="gameGrid.favor"></h2>
            <div id="favor-games"></div>
        </section>
    
        <section id="genre-game">
            <h2 data-lang="gameGrid.genreGame"></h2>
            <div id="genre-games"></div>
        </section>
    </div>
</main>

</body>

<script src="https://cdn.jsdelivr.net/npm/js-yaml@4.1.0/dist/js-yaml.min.js"></script>
<script src="assets/js/loadLang.js"></script>
<script src="assets/js/action.js"></script>

</html>