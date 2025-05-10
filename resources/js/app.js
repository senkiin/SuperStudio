import './bootstrap';
import { gsap } from "gsap";
import Sortable from 'sortablejs';

window.gsap = gsap;
window.Sortable = Sortable;
import Swiper from 'swiper';
import 'swiper/css'; // Import core Swiper CSS
import 'swiper/css/navigation'; // If using navigation arrows
import 'swiper/css/pagination'; // If using pagination dots
import PhotoSwipeLightbox from 'photoswipe/lightbox';
import PhotoSwipe from 'photoswipe';
window.PhotoSwipeLightbox = PhotoSwipeLightbox;

window.Swiper = Swiper; // Make it globally accessible or import where needed
