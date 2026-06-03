const modal = document.getElementById("loginModal");
const openButton = document.querySelector(".access-button");
const closeButton = document.getElementById("closeModalButton");

function openModal() {
  if (!modal) {
    return;
  }

  modal.classList.add("visible");
}

function closeModal() {
  if (!modal) {
    return;
  }

  modal.classList.remove("visible");
}

if (openButton) {
  openButton.addEventListener("click", openModal);
}

if (closeButton) {
  closeButton.addEventListener("click", closeModal);
}

if (modal) {
  modal.addEventListener("click", (event) => {
    if (event.target === modal) {
      closeModal();
    }
  });
}
