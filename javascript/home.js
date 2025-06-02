let currentSlide = 0;
const itemsPerPage = 10;
const totalItems = 25;
const carousel = document.getElementById("fictionCarousel");

function moveCarousel(direction) {
  const maxSlide = Math.ceil(totalItems / itemsPerPage) - 1;
  currentSlide += direction;

  if (currentSlide < 0) currentSlide = 0;
  if (currentSlide > maxSlide) currentSlide = maxSlide;

  const scrollAmount = currentSlide * (160 + 16) * itemsPerPage;
  carousel.scrollTo({ left: scrollAmount, behavior: "smooth" });
}

let currentBestSellerSlide = 0;
const bestSellerItemsPerPage = 2;
const totalBestSellerItems = 8;
const bestSellerCarousel = document.getElementById("bestSellerCarousel");

function moveBestSellerCarousel(direction) {
  const bestSellerCarousel = document.getElementById("bestSellerCarousel");
  const maxScroll = bestSellerCarousel.scrollWidth - bestSellerCarousel.clientWidth;
  const currentScroll = bestSellerCarousel.scrollLeft;

  // Move carousel
  if (direction === -1 && currentScroll > 0) {
    bestSellerCarousel.scrollLeft -= 300;  // Move left
  } else if (direction === 1 && currentScroll < maxScroll) {
    bestSellerCarousel.scrollLeft += 300;  // Move right
  }
}

let currentContinueSlide = 0;
const continueItemsPerPage = 3;
const totalContinueItems = 6;
const continueCarousel = document.getElementById("continueCarousel");

function moveContinueCarousel(direction) {
  const maxSlide = Math.ceil(totalContinueItems / continueItemsPerPage) - 1;
  currentContinueSlide += direction;

  if (currentContinueSlide < 0) currentContinueSlide = 0;
  if (currentContinueSlide > maxSlide) currentContinueSlide = maxSlide;

  const scrollAmount = currentContinueSlide * (300 + 20) * continueItemsPerPage;
  continueCarousel.scrollTo({ left: scrollAmount, behavior: "smooth" });}



let currentsSlide = 0;
const wattpadItemsPerPage = 2; // Two books per slide
const totalWattpadItems = 6;  // Number of books
const wattpadCarousel = document.getElementById("wattpadClassicsCarousel");

function moveWattpadClassicsCarousel(direction) {
  const maxSlide = Math.ceil(totalWattpadItems / wattpadItemsPerPage) - 1;
  currentsSlide += direction;

  if (currentsSlide < 0) currentsSlide = 0;
  if (currentsSlide > maxSlide) currentsSlide = maxSlide;

  const scrollAmount = currentsSlide * (wattpadCarousel.offsetWidth); // Calculate full carousel width
  wattpadCarousel.scrollTo({ left: scrollAmount, behavior: "smooth" });
}


let currentSlideFree = 0;
const itemsPerPageFree = 10;
const totalItemsFree = 25;
const carouselFree = document.getElementById("freeCarousel");

function moveFreeCarousel(direction) {
  const maxSlideFree = Math.ceil(totalItemsFree / itemsPerPageFree) - 1;
  currentSlideFree += direction;

  if (currentSlideFree < 0) currentSlideFree = 0;
  if (currentSlideFree > maxSlideFree) currentSlideFree = maxSlideFree;

  const scrollAmountFree = currentSlideFree * (160 + 16) * itemsPerPageFree;
  carouselFree.scrollTo({ left: scrollAmountFree, behavior: "smooth" });
}

// Top Premium Carousel
let currentSlidePremium = 0;
const itemsPerPagePremium = 10;
const totalItemsPremium = 25;
const carouselPremium = document.getElementById("premiumCarousel");

function movePremiumCarousel(direction) {
  const maxSlidePremium = Math.ceil(totalItemsPremium / itemsPerPagePremium) - 1;
  currentSlidePremium += direction;

  if (currentSlidePremium < 0) currentSlidePremium = 0;
  if (currentSlidePremium > maxSlidePremium) currentSlidePremium = maxSlidePremium;

  const scrollAmountPremium = currentSlidePremium * (160 + 16) * itemsPerPagePremium;
  carouselPremium.scrollTo({ left: scrollAmountPremium, behavior: "smooth" });
}


// Top Non-Fiction Carousel
let currentSlideNonFiction = 0;
const itemsPerPageNonFiction = 10;
const totalItemsNonFiction = 25;
const carouselNonFiction = document.getElementById("nonfictionCarousel");

function moveNonFictionCarousel(direction) {
  const maxSlideNonFiction = Math.ceil(totalItemsNonFiction / itemsPerPageNonFiction) - 1;
  currentSlideNonFiction += direction;

  if (currentSlideNonFiction < 0) currentSlideNonFiction = 0;
  if (currentSlideNonFiction > maxSlideNonFiction) currentSlideNonFiction = maxSlideNonFiction;

  const scrollAmountNonFiction = currentSlideNonFiction * (160 + 16) * itemsPerPageNonFiction;
  carouselNonFiction.scrollTo({ left: scrollAmountNonFiction, behavior: "smooth" });
}

// Top Science Fiction Carousel
let currentSlideScienceFiction = 0;
const itemsPerPageScienceFiction = 10;
const totalItemsScienceFiction = 25;
const carouselScienceFiction = document.getElementById("sciencefictionCarousel");

function moveScienceFictionCarousel(direction) {
  const maxSlideScienceFiction = Math.ceil(totalItemsScienceFiction / itemsPerPageScienceFiction) - 1;
  currentSlideScienceFiction += direction;

  if (currentSlideScienceFiction < 0) currentSlideScienceFiction = 0;
  if (currentSlideScienceFiction > maxSlideScienceFiction) currentSlideScienceFiction = maxSlideScienceFiction;

  const scrollAmountScienceFiction = currentSlideScienceFiction * (160 + 16) * itemsPerPageScienceFiction;
  carouselScienceFiction.scrollTo({ left: scrollAmountScienceFiction, behavior: "smooth" });
}

// Top Fantasy Carousel
let currentSlideFantasy = 0;
const itemsPerPageFantasy = 10;
const totalItemsFantasy = 25;
const carouselFantasy = document.getElementById("fantasyCarousel");

function moveFantasyCarousel(direction) {
  const maxSlideFantasy = Math.ceil(totalItemsFantasy / itemsPerPageFantasy) - 1;
  currentSlideFantasy += direction;

  if (currentSlideFantasy < 0) currentSlideFantasy = 0;
  if (currentSlideFantasy > maxSlideFantasy) currentSlideFantasy = maxSlideFantasy;

  const scrollAmountFantasy = currentSlideFantasy * (160 + 16) * itemsPerPageFantasy;
  carouselFantasy.scrollTo({ left: scrollAmountFantasy, behavior: "smooth" });
}

// Top Mystery Carousel
let currentSlideMystery = 0;
const itemsPerPageMystery = 10;
const totalItemsMystery = 25;
const carouselMystery = document.getElementById("mysteryCarousel");

function moveMysteryCarousel(direction) {
  const maxSlideMystery = Math.ceil(totalItemsMystery / itemsPerPageMystery) - 1;
  currentSlideMystery += direction;

  if (currentSlideMystery < 0) currentSlideMystery = 0;
  if (currentSlideMystery > maxSlideMystery) currentSlideMystery = maxSlideMystery;

  const scrollAmountMystery = currentSlideMystery * (160 + 16) * itemsPerPageMystery;
  carouselMystery.scrollTo({ left: scrollAmountMystery, behavior: "smooth" });
}

// Top Romance Carousel
let currentSlideRomance = 0;
const itemsPerPageRomance = 10;
const totalItemsRomance = 25;
const carouselRomance = document.getElementById("romanceCarousel");

function moveRomanceCarousel(direction) {
  const maxSlideRomance = Math.ceil(totalItemsRomance / itemsPerPageRomance) - 1;
  currentSlideRomance += direction;

  if (currentSlideRomance < 0) currentSlideRomance = 0;
  if (currentSlideRomance > maxSlideRomance) currentSlideRomance = maxSlideRomance;

  const scrollAmountRomance = currentSlideRomance * (160 + 16) * itemsPerPageRomance;
  carouselRomance.scrollTo({ left: scrollAmountRomance, behavior: "smooth" });
}

// Top Horror Carousel
let currentSlideHorror = 0;
const itemsPerPageHorror = 10;
const totalItemsHorror = 25;
const carouselHorror = document.getElementById("horrorCarousel");

function moveHorrorCarousel(direction) {
  const maxSlideHorror = Math.ceil(totalItemsHorror / itemsPerPageHorror) - 1;
  currentSlideHorror += direction;

  if (currentSlideHorror < 0) currentSlideHorror = 0;
  if (currentSlideHorror > maxSlideHorror) currentSlideHorror = maxSlideHorror;

  const scrollAmountHorror = currentSlideHorror * (160 + 16) * itemsPerPageHorror;
  carouselHorror.scrollTo({ left: scrollAmountHorror, behavior: "smooth" });
}
