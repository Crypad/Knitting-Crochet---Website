import { useEffect, useState } from "react";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faRotateLeft, faHouse, faCoffee, faUtensils, faLandmark, faDog, faFaucetDrip, faMasksTheater, faGasPump, faSquareParking, faHospital, faPiggyBank, faBasketShopping, faStethoscope, faEnvelopesBulk, faRestroom, faFilm } from "@fortawesome/free-solid-svg-icons";
import './RollMenu.css';

const CircleMenu = () => {
  const [dispMenu, setDispMenu] = useState(false);
  const icons = [
    faCoffee, faUtensils, faLandmark, faDog, faFaucetDrip, faMasksTheater, faGasPump, faSquareParking, faHospital, faPiggyBank, faBasketShopping, faStethoscope, faEnvelopesBulk, faRestroom, faFilm
  ];
  const zoomIn = (e) => {
    //get transform property value
    const style = window.getComputedStyle(e.target);
    const matrix = new WebKitCSSMatrix(style.transform);
    //get translate value
    const translateX = matrix.m41;
    const translateY = matrix.m42;
    //set translate value
    e.target.style.transform = "scale(1.5) translate(" + translateX + "px, " + translateY + "px)";
    e.target.style.transition = "transform 0.5s";
  };
  const zoomOut = (e) => {
    //get transform property value
    const style = window.getComputedStyle(e.target);
    const matrix = new WebKitCSSMatrix(style.transform);
    //get translate value
    const translateX = matrix.m41;
    const translateY = matrix.m42;
    //set translate value
    e.target.style.transform = "scale(1) translate(" + translateX + "px, " + translateY + "px)";

  };
  const displayRollMenu = () => setDispMenu(!dispMenu);

  useEffect(() => {
    if (dispMenu) {
      const items = document.querySelectorAll(".circleMenuItem");
      const total = items.length;
      const angleStep = (2 * Math.PI) / total;

      items.forEach((item, index) => {
        const angle = index * angleStep;
        const x = Math.cos(angle) * 150; // Rayon du cercle
        const y = Math.sin(angle) * 150;
        item.style.transform = `translate(${x}px, ${y}px) translate(-50%, -50%)`;
      });
      
    }
  }, [dispMenu]);

  return (
    <div className='rollMenu'>
      <div className='rollCenter'>
        <div className='rollCenterBtn'>
          <FontAwesomeIcon onClick={displayRollMenu} icon={dispMenu ? faRotateLeft : faHouse} color="blue" size="2x" />
        </div>
        {dispMenu && (
          <div className='circleMenu'>
            {icons.map((icon, index) => (
              <div className="circleMenuItem" key={index} onMouseOver={zoomIn} onMouseOut={zoomOut}>
                <FontAwesomeIcon icon={icon} color="red" size="2x" />
              </div>
            ))}
          </div>
        )}
      </div>
    </div>
  );
};

export default CircleMenu;
