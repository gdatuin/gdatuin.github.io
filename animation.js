document.addEventListener("DOMContentLoaded", (event) => {
  let observer = new IntersectionObserver(
    (entries, observer) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add("fade-in");
          observer.unobserve(entry.target);
        }
      });
    },
    { threshold: 0.1 }
  ); // Threshold defines how much of the item must be visible for the animation to start

  document
    .querySelectorAll(".listOfProducts .productItem")
    .forEach((item, index) => {
      item.style.animationDelay = `${0.2 * (index + 1)}s`; // Staggered delay
      observer.observe(item); // Start observing the item
    });
});
