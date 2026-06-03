const modal = document.getElementById("loginModal");
const openButton = document.querySelector(".access-button");

openButton.addEventListener("click", () => {
  modal.classList.add("visible");
});

modal.addEventListener("click", (event) => {
  if (event.target === modal) {
    modal.classList.remove("visible");
  }
});
