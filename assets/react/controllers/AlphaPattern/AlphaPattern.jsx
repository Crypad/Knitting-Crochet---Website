import React, { useEffect, useState, useRef } from 'react';
import html2canvas from 'html2canvas';
import './AlphaPattern.css';

const AlphaPattern = (props) => {
    const [gridSize, setGridSize] = useState([5, 5]);
    const [grid, setGrid] = useState([]);
    const [cellSize, setCellSize] = useState(30);
    const [selectedColor, setSelectedColor] = useState("#ff0000"); // Couleur par défaut : rouge
    const [lastColors, setLastColors] = useState(["#ffffff", "#ffffff", "#ffffff", "#ffffff", "#ffffff"]);
    const [savedFileName, setSavedFileName] = useState("alpha-pattern");
    const [isApressed, setIsApressed] = useState(false);
    const isFirstRender = useRef(true);
    const containerRef = useRef(null);

    useEffect(() => {
        if (isFirstRender.current) {
            isFirstRender.current = false;
            generateGrid();
            return;
        }
        updateGridSize();
    }, [gridSize, cellSize]);

    const handleSavePopUp = () => {
        if (!containerRef.current) return;

        document.querySelector(".savePopUp").classList.toggle("saveVisible");
        document.querySelector(".savePopUp").classList.toggle("saveHidden");
    }

    const toolBarToggle = () => {
        const toolBar = document.querySelector(".toolContainer");
        toolBar.classList.toggle("toolBarVisible");
        toolBar.classList.toggle("toolBarHidden");
    };

    const infoToggle = () => {
        const info = document.querySelector(".infoContainer")
        info.classList.toggle("infoVisible");
        info.classList.toggle("infoHidden");
    }

    const generateGrid = () => {
        const newGrid = [];
        for (let i = 0; i < gridSize[0]; i++) {
            const row = [];
            for (let j = 0; j < gridSize[1]; j++) {
                row.push({
                    id: `${i}-${j}`,
                    color: "white" // Couleur par défaut des cellules
                });
            }
            newGrid.push(row);
        }
        setGrid(newGrid);
    };

    const updateGridSize = () => {
        setGrid((prevGrid) => {
            const newGrid = [];

            for (let i = 0; i < gridSize[0]; i++) {
                const row = [];

                for (let j = 0; j < gridSize[1]; j++) {
                    if (prevGrid[i] && prevGrid[i][j]) {
                        // Conserver la couleur existante
                        row.push(prevGrid[i][j]);
                    } else {
                        // Nouvelle cellule (si agrandissement)
                        row.push({
                            id: `${i}-${j}`,
                            color: "white"
                        });
                    }
                }

                newGrid.push(row);
            }

            return newGrid;
        });
    };

    const downloadScreenshot = async () => {
        if (!containerRef.current) return;

        const canvas = await html2canvas(containerRef.current, { useCORS: true });
        const image = canvas.toDataURL("image/png");

        // Créer un lien pour télécharger l'image
        const link = document.createElement("a");
        link.href = image;
        link.download = `${savedFileName}.png`;
        link.click();
        handleSavePopUp();
    };

    const saveScreenshot = async () => {
        if (!containerRef.current) return;

        const canvas = await html2canvas(containerRef.current, { useCORS: true });
        const imageData = canvas.toDataURL("image/png"); // Convertir en Base64

        console.dir(imageData);

        // Envoi de l'image au backend
        fetch(props.fetchUrl, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ image: imageData, fileName: savedFileName })
        })
            .then(response => response.json())
            .then((data) => {
                console.log("Image enregistrée :", data);
                handleSavePopUp();
            })
            .catch(error => console.error("Erreur lors de l'envoi :", error));
    };

    const handleCellClick = (rowIndex, colIndex) => {
        setGrid((prevGrid) => {
            const newGrid = [...prevGrid];
            newGrid[rowIndex][colIndex] = {
                ...newGrid[rowIndex][colIndex],
                color: selectedColor
            };
            return newGrid;
        });
    };

    const handleColorHover = (rowIndex, colIndex) => {
        if (isApressed) {
            setGrid((prevGrid) => {
                const newGrid = [...prevGrid];
                newGrid[rowIndex][colIndex] = {
                    ...newGrid[rowIndex][colIndex],
                    color: selectedColor
                };
                return newGrid;
            });
        }
    };

    useEffect(() => {

        const handleKeyDown = (event) => {
            if (event.key === "a") {
                console.log("a pressed");
                setIsApressed(true);
            }
        };

        const handleKeyUp = (event) => {
            if (event.key === "a") {
                console.log("a released");
                setIsApressed(false);
            }
        };

        window.addEventListener("keydown", handleKeyDown);
        window.addEventListener("keyup", handleKeyUp);

        // Nettoyage de l'effet pour éviter les fuites mémoire
        return () => {
            window.removeEventListener("keydown", handleKeyDown);
            window.removeEventListener("keyup", handleKeyUp);
        };
    }, []);

    return (
        <div className='APcontainer'>

            <div className="savePopUp saveHidden">
                <div className="savePopUpContainer">
                    <div id="closeIcon">
                        <i className="fa-solid fa-square-xmark" onClick={handleSavePopUp}></i>
                    </div>
                    <h2>Enregistrer le modèle</h2>
                    <div className='saveInputContainer'>
                        <input className='saveInputName' type="text" placeholder="Nom du pattern..." onChange={(e) => setSavedFileName(e.target.value)} />
                    </div>
                    <div className="savePopUpButtons">
                        <button className='btnPrimary' onClick={() => {
                            setSavedFileName(document.querySelector(".saveInputName").value);
                            saveScreenshot();
                        }}>Enregistrer</button>
                        <button className='btnPrimary' onClick={() => {
                            setSavedFileName(document.querySelector(".saveInputName").value);
                            downloadScreenshot();
                        }}>Telecharger</button>
                    </div>
                </div>
            </div>

            <h1>Alpha Pattern Creator</h1>

            <div className="toolButton" onClick={toolBarToggle}>
                <i className="fa-solid fa-wrench"></i>
            </div>

            <div className="infoButton" onClick={infoToggle}>
                <i className="fa-solid fa-info"></i>
            </div>

            <div className="infoContainer infoHidden">
                <p>- Vous pouvez librement dessiner en maintenant la touche "A" et en survolant les cellules.</p>
                <br />
                <p>- Une fois enregistrée, vous retrouverez toutes vos créations sur l'onglet "Mon profil".</p>
            </div>

            <div className="toolContainer toolBarHidden">

                <div className="gridSizeContainer">
                    <div className='toolDiv'>
                        <label>↕️ : </label>
                        <input
                            type="number"
                            value={gridSize[0]}
                            onChange={(e) => setGridSize([Math.max(1, Number(e.target.value)), gridSize[1]])}
                        />
                    </div>
                    <div className="toolDiv">
                        <label>↔️ : </label>
                        <input
                            type="number"
                            value={gridSize[1]}
                            onChange={(e) => setGridSize([gridSize[0], Math.max(1, Number(e.target.value))])}
                        />
                    </div>
                </div>

                <div className="cellSizeContainer">
                    <label>Taille 🔲 (px) :</label>
                    <input
                        type="number"
                        value={cellSize}
                        min={10}
                        max={100}
                        onChange={(e) => setCellSize(Math.max(10, Number(e.target.value)))}
                    />
                </div>

                <div className="colorPickerContainer">
                    <label>Couleur :</label>
                    <input
                        type="color"
                        value={selectedColor}
                        onChange={(e) => {
                            setSelectedColor(e.target.value);
                        }}
                    />
                </div>

                <div className="lastPickedColor">
                    <label>Dernières Couleur :</label>
                    <div className="lastPickedColorContainer">
                        {lastColors.map((color, index) => (
                            <div className="lastPickedColorBox" id={color} key={index} style={{ backgroundColor: color }} onClick={() => setSelectedColor(color)}></div>
                        ))}
                    </div>
                </div>
            </div>


            <div className="testGrid">
                <div className="alphaPatternContainer" ref={containerRef}>
                    <div className="columnLabels">
                        <div className="corner" style={{ width: `${cellSize}px` }}></div>
                        {[...Array(gridSize[1])].map((_, index) => (
                            <div key={index} className="columnLabel" style={{ width: `${cellSize}px` }}>
                                {index + 1}
                            </div>
                        ))}
                    </div>
                    <div className="gridContainer">
                        {grid.map((row, rowIndex) => (
                            <div key={rowIndex} className="row">
                                <div className="rowLabel" style={{ width: `${cellSize}px`, height: `${cellSize}px` }}>
                                    {rowIndex + 1}
                                </div>
                                {row.map((cell, colIndex) => (
                                    <div
                                        key={cell.id}
                                        className="cell"
                                        style={{
                                            width: `${cellSize}px`,
                                            height: `${cellSize}px`,
                                            backgroundColor: cell.color
                                        }}
                                        onClick={() => {
                                            handleCellClick(rowIndex, colIndex)
                                            if (lastColors[lastColors.length - 1] !== selectedColor && lastColors.includes(selectedColor) === false) {
                                                const lastColorsCopy = [...lastColors];
                                                lastColorsCopy.shift();
                                                lastColorsCopy.push(selectedColor);
                                                setLastColors(lastColorsCopy);
                                            }
                                        }}
                                        onMouseOver={() => {
                                            handleColorHover(rowIndex, colIndex);
                                            if (lastColors[lastColors.length - 1] !== selectedColor && lastColors.includes(selectedColor) === false) {
                                                const lastColorsCopy = [...lastColors];
                                                lastColorsCopy.shift();
                                                lastColorsCopy.push(selectedColor);
                                                setLastColors(lastColorsCopy);
                                            }
                                        }}

                                    ></div>
                                ))}
                            </div>
                        ))}
                    </div>
                </div>
            </div>

            <div className="downloadSave">
                <button className='btnPrimary' onClick={handleSavePopUp}>📥 Télécharger ou sauvegarder le modèle</button>
            </div>
        </div>
    );
};

export default AlphaPattern;
