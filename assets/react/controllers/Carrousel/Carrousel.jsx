import React, { useEffect, useState } from 'react'
import Carousel from 'react-multi-carousel';
import 'react-multi-carousel/lib/styles.css';
import './Carrousel.css';

const Carrousel = () => {


    return (
        <div className="carousel-container">
            <Carousel
                additionalTransfrom={0}
                arrows
                autoPlay
                autoPlaySpeed={3000}
                centerMode={false}
                className=""
                containerClass="carousel"
                dotListClass=""
                draggable
                focusOnSelect={false}
                infinite
                itemClass="homeCarouselItem"
                keyBoardControl
                minimumTouchDrag={80}
                renderArrowsWhenDisabled={false}
                renderButtonGroupOutside={false}
                renderDotsOutside={false}
                responsive={{
                    all: {
                        breakpoint: { max: 5000, min: 0 },
                        items: 1
                    }
                }}
                rewind={false}
                rewindWithAnimation={false}
                rtl={false}
                shouldResetAutoplay
                showDots
                sliderClass=""
                slidesToSlide={1}
                swipeable
            >
                <img
                    src="https://images.unsplash.com/photo-1549989476-69a92fa57c36?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=800&q=60"
                    style={{
                        display: 'block',
                        height: '100%',
                        margin: 'auto',
                        width: '100%'
                    }}
                />
                <img
                    src="https://images.unsplash.com/photo-1549396535-c11d5c55b9df?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=60"
                    style={{
                        display: 'block',
                        height: '100%',
                        margin: 'auto',
                        width: '100%'
                    }}
                />
                <img
                    src="https://images.unsplash.com/photo-1550133730-695473e544be?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=800&q=60"
                    style={{
                        display: 'block',
                        height: '100%',
                        margin: 'auto',
                        width: '100%'
                    }}
                />
                <img
                    src="https://images.unsplash.com/photo-1550167164-1b67c2be3973?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=800&q=60"
                    style={{
                        display: 'block',
                        height: '100%',
                        margin: 'auto',
                        width: '100%'
                    }}
                />
                <img
                    src="https://images.unsplash.com/photo-1550338861-b7cfeaf8ffd8?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=800&q=60"
                    style={{
                        display: 'block',
                        height: '100%',
                        margin: 'auto',
                        width: '100%'
                    }}
                />
            </Carousel>
        </div>
    )
}

export default Carrousel