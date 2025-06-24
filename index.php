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

    <section id="game-grid">
        <h2 data-lang="gameGrid.all"></h2>
    
        <section id="favor-game-grid">
            <h3 data-lang="gameGrid.favor"></h3>
    
        </section>
    
        <section id="newest-game-grid">
            <h3 data-lang="gameGrid.newest"></h3>
    
        </section>
    </section>
</main>

</body>

<script src="https://cdn.jsdelivr.net/npm/js-yaml@4.1.0/dist/js-yaml.min.js"></script>
<script src="assets/js/loadLang.js"></script>
<script src="assets/js/action.js"></script>

</html>