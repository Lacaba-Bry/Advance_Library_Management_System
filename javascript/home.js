
// Generic horizontal scroll function
function scrollCarousel(carouselId, stateObj, direction, itemWidth, itemGap, itemsPerPage, totalItems) {
  const carousel = document.getElementById(carouselId);
  if (!carousel) return;

  const maxSlide = Math.ceil(totalItems / itemsPerPage) - 1;
  stateObj.current += direction;
  if (stateObj.current < 0) stateObj.current = 0;
  if (stateObj.current > maxSlide) stateObj.current = maxSlide;

  const scrollAmount = stateObj.current * (itemWidth + itemGap) * itemsPerPage;
  carousel.scrollTo({ left: scrollAmount, behavior: "smooth" });
}

// Best Seller (2 per page)
const bestSellerState = { current: 0 };
function moveBestSellerCarousel(direction) {
  scrollCarousel("bestSellerCarousel", bestSellerState, direction, 430, 20, 2, 8);
}

// Fiction
const fictionState = { current: 0 };
function moveCarousel(direction) {
  scrollCarousel("fictionCarousel", fictionState, direction, 160, 16, 10, 25);
}

// Continue Reading
const continueState = { current: 0 };
function moveContinueCarousel(direction) {
  scrollCarousel("continueCarousel", continueState, direction, 300, 20, 3, 6);
}

// Wattpad
const wattpadState = { current: 0 };
function moveWattpadClassicsCarousel(direction) {
  const carousel = document.getElementById("wattpadClassicsCarousel");
  if (!carousel) return;

  const maxSlide = Math.ceil(6 / 2) - 1;
  wattpadState.current += direction;
  if (wattpadState.current < 0) wattpadState.current = 0;
  if (wattpadState.current > maxSlide) wattpadState.current = maxSlide;

  const scrollAmount = wattpadState.current * carousel.offsetWidth;
  carousel.scrollTo({ left: scrollAmount, behavior: "smooth" });
}

// Free Books
const freeState = { current: 0 };
function moveFreeCarousel(direction) {
  scrollCarousel("freeCarousel", freeState, direction, 160, 16, 10, 25);
}

// Premium Books
const premiumState = { current: 0 };
function movePremiumCarousel(direction) {
  scrollCarousel("premiumCarousel", premiumState, direction, 160, 16, 10, 25);
}

// Nonfiction
const nonFictionState = { current: 0 };
function moveNonFictionCarousel(direction) {
  scrollCarousel("nonfictionCarousel", nonFictionState, direction, 160, 16, 10, 25);
}

// Science Fiction
const scienceFictionState = { current: 0 };
function moveScienceFictionCarousel(direction) {
  scrollCarousel("sciencefictionCarousel", scienceFictionState, direction, 160, 16, 10, 25);
}

// Fantasy
const fantasyState = { current: 0 };
function moveFantasyCarousel(direction) {
  scrollCarousel("fantasyCarousel", fantasyState, direction, 160, 16, 10, 25);
}

// Mystery
const mysteryState = { current: 0 };
function moveMysteryCarousel(direction) {
  scrollCarousel("mysteryCarousel", mysteryState, direction, 160, 16, 10, 25);
}

// Romance
const romanceState = { current: 0 };
function moveRomanceCarousel(direction) {
  scrollCarousel("romanceCarousel", romanceState, direction, 160, 16, 10, 25);
}

// Horror
const horrorState = { current: 0 };
function moveHorrorCarousel(direction) {
  scrollCarousel("horrorCarousel", horrorState, direction, 160, 16, 10, 25);
}
