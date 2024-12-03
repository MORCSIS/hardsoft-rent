function toggleMenu() {
    let sidebar = document.getElementById("sidebar");
    let mainContent = document.getElementById("main-content");

    if (sidebar.style.left === "-250px" || sidebar.style.left === "") {
        sidebar.style.left = "0";
        mainContent.style.marginLeft = "250px";
    } else {
        sidebar.style.left = "-250px";
        mainContent.style.marginLeft = "0";
    }
}
