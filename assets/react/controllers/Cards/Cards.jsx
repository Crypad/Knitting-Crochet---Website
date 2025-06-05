import React, { useEffect, useState } from 'react';
import Carousel from 'react-multi-carousel';
import 'react-multi-carousel/lib/styles.css';
import './Cards.css';

const Cards = (props) => {
    const [cards, setCards] = useState(null);
    const [instagramPubs, setInstagramPubs] = useState(null);
    const [annonce, setAnnonce] = useState(null);

    useEffect(() => {
        fetch(props.fetchUrl, { method: 'GET' })
            .then(response => response.json())
            .then(data => {
                console.dir(data);
                setCards(data);
            })
            .catch(error => console.dir(error));
    }, [props.fetchUrl]);

    useEffect(() => {
        fetch(props.fetchInstaUrl, { method: 'GET' })
            .then(response => response.json())
            .then(data => {
                console.dir(data);
                setInstagramPubs(data);
            })
            .catch(error => console.dir(error));
    }, [props.fetchInstaUrl]);

    useEffect(() => {
        fetch(props.fetchAnnonceUrl, { method: 'GET' })
            .then(response => response.json())
            .then(data => {
                console.dir(data);
                setAnnonce(data);
            })
            .catch(error => console.dir(error));
    }, [props.fetchAnnonceUrl]);

    // Effect pour forcer le traitement des embeds Instagram une fois les publications chargées
    useEffect(() => {
        if (
            instagramPubs &&
            window.instgrm &&
            window.instgrm.Embeds &&
            typeof window.instgrm.Embeds.process === 'function'
        ) {
            window.instgrm.Embeds.process();
        }
    }, [instagramPubs]);

    if (cards === null) {
        return <h2>Loading...</h2>;
    }

    return (
        <>
            <h2 className='cardsTitleH2'>Mes dernières créations :</h2>
            <div className='cards'>
                {cards.map((element) => (
                    <div className='card' key={element.id}>
                        <div className="carousel-container-card">
                            <Carousel
                                itemClass='my-carousel-item'
                                additionalTransfrom={0}
                                arrows
                                autoPlaySpeed={3000}
                                centerMode={false}
                                containerClass="container"
                                draggable
                                infinite
                                keyBoardControl
                                minimumTouchDrag={80}
                                pauseOnHover
                                responsive={{
                                    all: {
                                        breakpoint: { max: 5000, min: 0 },
                                        items: 1
                                    }
                                }}
                                showDots
                                slidesToSlide={1}
                                swipeable
                            >
                                {element.images.map((image, idx) => (
                                    <img
                                        key={idx}
                                        src={props.assetUrl + image}
                                        style={{ display: 'block', height: '100%', margin: 'auto', width: '400px' }}
                                        alt=""
                                    />
                                ))}
                            </Carousel>
                        </div>
                        <div className='cardInfo'>
                            <h2>{element.content[0]}</h2>
                            <hr />
                            <p>{element.content[1]}</p>
                        </div>
                    </div>
                ))}
            </div>
            <h2 className='cardsTitleH2'>Ce que vous pouvez trouver sur instagram</h2>
            <div className="instaPubsContainer">
                {instagramPubs && instagramPubs.map((element) => (
                    <div
                        className="instaPub"
                        key={element.id}
                        dangerouslySetInnerHTML={{ __html: element.content }}
                    />
                ))}
            </div>
            <div className="annonceContainer">
                <div className="annonce">
                    <h2 className='cardsTitleH2'>Annonce</h2>
                    <div className='annonceContent' dangerouslySetInnerHTML={{ __html: annonce }}>

                    </div>
                </div>
            </div>
        </>
    );
};

export default Cards;