function toggleFaq(element) {
    const answer = element.nextElementSibling;
    const toggleIcon = element.querySelector('.ct-faq-toggle');

    if (answer.style.display === "none" || answer.style.display === "") {
      answer.style.display = "block";
      toggleIcon.textContent = "-";
    } else {
      answer.style.display = "none";
      toggleIcon.textContent = "+";
    }
  }