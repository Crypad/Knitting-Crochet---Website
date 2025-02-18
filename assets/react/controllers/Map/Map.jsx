import React, { useEffect, useState } from 'react'
import RollMenu from '../RollMenu/RollMenu';
import { MapContainer, TileLayer, Popup, useMap } from 'react-leaflet'
import { MarkerLayer, Marker } from "react-leaflet-marker";
import './Map.css';

function Map() {
    //const map = useMap();
    const [coords, setCoords] = useState([51.505,-0.09]);
    // get geolocation
    useEffect(() => {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition((position) => {
                console.log(position.coords.latitude, position.coords.longitude);
                setCoords([position.coords.latitude,position.coords.longitude]);
            });
        } else {
            console.log("Geolocation is not supported by this browser.");
        }

    }, []);
    return (
        <div className='map'>
            <MapContainer 
            style={{ height: "100%", minHeight: "100%" }}
            center={coords} 
            zoom={15} 
            scrollWheelZoom={true}
            >
                <TileLayer
                    attribution='&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                    url="https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
                />
                <MarkerLayer>
            <Marker
                position={coords}
            >
                <RollMenu/>
            </Marker>
        </MarkerLayer>
                
            </MapContainer>
        </div>
    )
}
export default Map
