import React, { useEffect, useState } from 'react';
import './Footer.css'

const Footer = (props) => {

    return (
        <div className='footer'>
            <div className="footerSectionContainer">
                <div className="footerSection">
                    <h2>A propos de nous</h2>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate, consequatur. Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate, consequatur.</p>
                    <img src={props.assetUrl + 'whoolLogo.png'} alt="" />
                </div>
                <div className="footerSection">
                    <h2>Contact</h2>
                    <p><i class="fa-solid fa-location-dot"></i> Adresse : 14 rue du Boeuf, 76200, Dieppe.</p>
                    <p><i class="fa-solid fa-phone-volume"></i> Téléphone : 06 45 34 49 43</p>
                    <p><i class="fa-solid fa-comment"></i> Mail : erwanncrevel.ec@gmail.com</p>
                </div>
                <div className="footerSection">
                    <h2>Liens importants</h2>
                    <a href="/"><i class="fa-solid fa-house"></i> Acceuil</a>
                    <a href="/register"><i class="fa-solid fa-circle-user"></i> S'enregistrer</a>
                    <a href="/faq"><i class="fa-solid fa-comment"></i> FAQ</a>
                    <a href="/cgu"><i class="fa-solid fa-book"></i> CGU</a>
                </div>
                <div className="footerSection">
                    <h2>Newsletter</h2>
                    <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate, consequatur. Lorem ipsum dolor sit amet consectetur adipisicing elit. Voluptate, consequatur.</p>
                    <div className="newsletter">
                        <input type="email" placeholder='adresse mail'></input><i class="fa-solid fa-paper-plane newsletterBtn"></i>
                    </div>
                </div>
            </div>
            <hr className='footerHr' />
            <div className="socialMedia">
                <div className="copyright">Copyright ©2025 Whool All rights reserved | This site has been made by Grille-Pain</div>
                <div className='socialMediaIcons'>
                    <a href=""><i class="fa-brands fa-facebook"></i></a>
                    <a href=""><i class="fa-brands fa-instagram"></i></a>
                    <a href=""><i class="fa-brands fa-linkedin"></i></a>
                    <a href=""><i class="fa-brands fa-twitter"></i></a>
                </div>
            </div>
        </div>
    )
}

export default Footer