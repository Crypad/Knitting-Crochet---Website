import React, { useEffect, useState } from "react";
import './Account.css'

const Account = (props) => {

    console.log(props.editUrl);
    console.log(props.propsUser);

    // States 
    const [visible, setVisible] = useState(false);
    const [editedInfos, setEditedInfos] = useState({});
    const [clickedImage, setClickedImage] = useState('');
    const [deleteId, setDeleteId] = useState('');

    useEffect(() => {
        if (visible) {
            document.querySelector(".editInfosContainer").classList.toggle("editVisible");
            document.querySelector(".editInfosContainer").classList.toggle("editHidden");
        } else {
            document.querySelector(".editInfosContainer").classList.toggle("editVisible");
            document.querySelector(".editInfosContainer").classList.toggle("editHidden");
        }
    }, [visible]);

    const handleDelete = () => {
        console.log(deleteId);
        
        fetch(props.modelDeleteUrl, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: deleteId })
        }).then(response => {
            return response.json();
        }).then(data => {
            console.log(data);
            window.location.reload();
        }).catch(error => {
            console.log(error);
        })
    }

    const modifyInfos = () => {
        console.log("-------------------------")
        console.log(editedInfos);
        console.log("-------------------------")

        fetch(props.editUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(editedInfos)
        }).then(response => {
            return response.json();
        }).then(data => {
            console.log(data);
            window.location.reload();
        }).catch(error => {
            console.log(error);
        })
    }

    const editImg = () => {
        console.log("click");
        document.getElementById('fileInput').click()

        document.getElementById('fileInput').onchange = () => {
            const file = document.getElementById('fileInput').files[0];
            console.log(file);
            console.log(props.editUrlImg);
            // Fetch the file and the user id and upload it to the server
            const formData = new FormData();
            formData.append('file', file);
            formData.append('id', infos.id);
            fetch(props.editUrlImg, {
                method: 'POST',
                body: formData
            }).then(response => {
                return response.json();
            }).then(data => {
                console.log(data);
                window.location.reload();
            }).catch(error => {
                console.log(error);
            })
        }

    }

    useEffect(() => {
        if (Object.keys(editedInfos).length > 0) {
            modifyInfos();
        }
    }, [editedInfos]);

    const handleFullScreen = () => {
        document.querySelector(".fullscreenImageContainer").classList.toggle("fullScreenHidden");
        document.querySelector(".fullscreenImageContainer").classList.toggle("fullScreenVisible");
    }


    const infos = props.propsUser;
    console.log(infos);
    const date = infos.birthdate.date;
    const formatedDate = date.split(" ")[0];

    return (
        <>
            {/* Fullscreen image onClick */}
            <div className="fullscreenImageContainer fullScreenHidden" onClick={handleFullScreen}>
                <div className="fullscreenDSD">
                    <button className="btnPrimary" onClick={() => {
                        handleDelete();
                    }}>Supprimer</button>
                    <button className="btnPrimary" onClick={() => {window.location.href = `${props.pathSharing}`}}>Partager</button>
                    <button className="btnPrimary">Telecharger</button>
                </div>
                <div className="fullscreenImage">
                    <img src={`${props.assetUrl}${clickedImage}`} alt="" />
                </div>
            </div>

            <div className="accountContainer">

                <div className="profileInfos">
                    <div id="accountImgContainer">
                        <div className="brrrrrrrrrr">
                            <img className="editImage" src={`/img/edit_logo.png`} alt="" onClick={editImg} ></img>
                            <img className="accountImage" src={`assets/profileImage/${infos.profileImage}`} alt=""></img>
                            <input type="file" id="fileInput" />
                        </div>
                    </div>
                    <div className="mesInformations">
                        <div className="groupInfo"><p className="accountLabel">Email : </p><span className="accountInfo">{infos.email}</span></div>
                        <div className="groupInfo"><p className="accountLabel">Pseudo : </p><span className="accountInfo">{infos.pseudo}</span></div>
                        <div className="groupInfo"><p className="accountLabel">Prénom : </p><span className="accountInfo">{infos.surname}</span></div>
                        <div className="groupInfo"><p className="accountLabel">Nom : </p><span className="accountInfo">{infos.name}</span></div>
                        <div className="groupInfo"><p className="accountLabel">Date de naissance : </p><span className="accountInfo">{formatedDate}</span></div>

                        <button className="btnPrimary" onClick={() => setVisible(!visible)}>Editer mes informations</button>
                    </div>
                </div>


                <div className="mesCreationsContainer">
                    <h2>Mes creations</h2>
                    <div className="mesCreations">
                        {infos.models.map((model) => {
                            return (
                                <div className="creation" id={model.image} key={model.id} onClick={() => {
                                    setClickedImage(model.image);
                                    setDeleteId(model.id);
                                    handleFullScreen();
                                }}>
                                    <p>{model.image.split(".")[0]}</p>
                                    <img src={`${props.assetUrl}${model.image}`} alt="" />
                                    <p>{model.createdAt["date"].split(".")[0]}</p>
                                </div>
                            )
                        })}
                    </div>
                </div>



                <div className="editInfosContainer editVisible">
                    <div className="editInfos">

                        <div id="closeIcon">
                            <i className="fa-solid fa-square-xmark" onClick={() => setVisible(!visible)}></i>
                        </div>

                        <p>Votre email : <span>{infos.email}</span></p>
                        <input className="editInput" name="email" type="text" id="editEmail"></input>
                        <p>Votre pseudo : <span>{infos.pseudo}</span></p>
                        <input className="editInput" name="pseudo" type="text" id="editPseudo"></input>
                        <p>Votre prénom : <span>{infos.surname}</span></p>
                        <input className="editInput" name="surname" type="text" id="editSurname"></input>
                        <p>Votre nom : <span>{infos.name}</span></p>
                        <input className="editInput" name="name" type="text" id="editName"></input>
                        <p>Votre date de naissance : <span>{formatedDate}</span></p>
                        <input className="editInput" name="birthdate" type="date" id="editBirthdate"></input>
                        <button className="btnPrimary" onClick={() => {
                            setEditedInfos({
                                id: infos.id,
                                email: document.querySelector("#editEmail").value,
                                pseudo: document.querySelector("#editPseudo").value,
                                surname: document.querySelector("#editSurname").value,
                                name: document.querySelector("#editName").value,
                                birthdate: document.querySelector("#editBirthdate").value
                            });
                            setVisible(!visible);
                        }}>Valider</button>
                    </div>
                </div>
            </div>
        </>
    )
}

export default Account