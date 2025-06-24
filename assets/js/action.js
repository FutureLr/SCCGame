document.querySelector("#genre-toggle-btn").addEventListener("click", ()=>{
    let genreList = document.querySelector("#genre-list");
    let gameGrid = document.querySelector("#game-grid");
    if (genreList.classList.contains("active")){
        gameGrid.classList.remove("with-sidebar");
        genreList.classList.remove("active");
    } else {
        gameGrid.classList.add("with-sidebar");
        genreList.classList.add("active");
    }
});