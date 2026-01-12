import Swiper from 'swiper';
import { Navigation } from 'swiper/modules';

import 'swiper/css';
import 'swiper/css/navigation';
import { FreeMode } from 'swiper/modules';
document.addEventListener('DOMContentLoaded', function () {
    if(document.querySelector('.slider')){
        const opciones = {
            slidesPerView: 1,
            spaceBetween: 20,
            FreeMode: true,
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
            breakpoints: {
                768: {
                    slidesPerView: 2,
                    spaceBetween: 20
                },
                1024:{
                    slidesPerView: 3,
                    spaceBetween: 20
                },
                1200:{
                    slidesPerView: 4,
                    spaceBetween: 20
                }
            }
        }
        Swiper.use([Navigation])
        new Swiper('.slider', opciones);
    }
});
