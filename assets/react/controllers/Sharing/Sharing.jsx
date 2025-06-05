import React, { useEffect, useState, useRef } from "react";
import { Editor } from '@tinymce/tinymce-react';
import Carousel from 'react-multi-carousel';
import 'react-multi-carousel/lib/styles.css';
import './Sharing.css'


const Sharing = (props) => {

    const [search, setSearch] = useState(null);
    const [searchPressed, setSearchPressed] = useState(false);
    const [tags, setTags] = useState([]);
    const [visiblePopUp, setVisiblePopUp] = useState(false);
    const [fetchedModels, setFetchedModels] = useState([]);
    const [selectedModel, setSelectedModel] = useState([]);
    const [selectedTags, setSelectedTags] = useState([]);
    const [allPublications, setAllPublications] = useState([]);
    const [selectedPublication, setSelectedPublication] = useState(null); // For pop-up
    const [newComment, setNewComment] = useState('');
    const [temporaryComment, setTemporaryComment] = useState('');
    const [newCommentIsAnswer, setNewCommentIsAnswer] = useState(false);
    const [allComments, setAllComments] = useState([]);
    const [getCommentToggle, setGetCommentToggle] = useState(false);
    const [answerInputToggle, setAnswerInputToggle] = useState(false);
    const [answerId, setAnswerId] = useState(null);
    const [userPseudo, setUserPseudo] = useState(null);

    const editorRef = useRef(null);
    const isFirstRender = useRef(true);

    /* ---------- Fetch New Publications by tag ---------- */

    useEffect(() => {
        if (searchPressed) {
            if (search === "") {
                setAllPublications(props.allPublications);
            } else {
                fetch(props.fetchPublicationsByTagUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ tag: search })
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log("Fetched publications by tag:", data);
                        setSearchPressed(false);
                        setAllPublications(data.publications);
                    })
                    .catch(error => {
                        console.log(error);
                    })
            }
        }

    }, [searchPressed]);

    useEffect(() => {
        fetch(props.fetchCurrentUserUrl, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                console.log("User ID:", data);
                setUserPseudo(data.userPseudo);
            })
            .catch(error => {
                console.log(error);
            })
    }, [props.fetchCurrentUserUrl]);

    /* ---------- Fetch Publications ---------- */

    useEffect(() => {
        setAllPublications(props.allPublications);
        console.log(props.allPublications);
    }, [props.allPublications]);

    /* ---------- Fetch Models ---------- */

    useEffect(() => {
        if (isFirstRender.current) {
            isFirstRender.current = false;
            fetch(props.fetchModelsUrl, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                }
            }).then(response => response.json())
                .then(data => {
                    console.log("Fetched models:", data);
                    setFetchedModels(data);
                })
                .catch(error => console.log(error));
        }
    }, []);

    /* ---------- Fetch Tags ---------- */

    useEffect(() => {
        if (search !== null) {
            if (search === '') {
                document.querySelector(".tagsContainer").classList.add("tagsDisplayNone");
                setTags([]);
                return;
            } else {
                document.querySelector(".tagsContainer").classList.remove("tagsDisplayNone");
                fetch(props.fetchTagsUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ prefix: search })
                }).then(response => response.json())
                    .then(data => {
                        console.log("Fetched tags:", data);
                        setTags(data.tagsList);
                    })
                    .catch(error => console.log(error));
            }
        }
    }, [search]);

    useEffect(() => {
        console.log(tags);
    }, [tags]);

    /* ---------- Functions ---------- */

    // Add a selected model
    const handleModels = () => {
        const selectedValue = document.querySelector(".modelsSelect").value;
        if (selectedModel.length >= 3) {
            alert("Vous avez atteint la limite de modèles.");
            return
        }
        if (selectedModel.includes(selectedValue)) {
            alert("Le modèle est deja ajouté.");
            return
        }
        if (selectedValue !== '') {
            setSelectedModel((prevModels) => [...prevModels, selectedValue]);
        } else {
            alert("Le modèle ne peut pas être vide.");
        }
    };

    // Add a tag
    const handleTags = () => {
        const tagValue = document.querySelector(".tagsInput").value;
        if (selectedTags.length >= 8) {
            alert("Vous avez atteint la limite de tags.");
            return
        }
        if (selectedTags.includes(tagValue)) {
            alert("Le tag est deja ajouté.");
            return
        }
        if (tagValue !== '') {
            setSelectedTags((prevTags) => [...prevTags, tagValue]);
            document.querySelector(".tagsInput").value = '';
        } else {
            alert("Le tag ne peut pas être vide.");
        }
    };

    // Delete a tag
    const handleDeleteTag = (tagToDelete) => {
        setSelectedTags((prevTags) => prevTags.filter((tag) => tag !== tagToDelete));
    };

    // Delete a model
    const handleDeleteModel = (modelToDelete) => {
        setSelectedModel((prevModels) => prevModels.filter((model) => model !== modelToDelete));
    };

    // Save TinyMCE content
    const handleSave = () => {
        if (editorRef.current) {
            const editorContent = editorRef.current.getContent(); // Get latest TinyMCE content

            // Get selected file
            const fileInput = document.querySelector(".uploadInput");
            if (!fileInput.files.length) {
                alert("Veuillez sélectionner une image.");
                return;
            }
            const selectedFile = fileInput.files[0];

            // Create FormData to handle file upload
            const formData = new FormData();
            formData.append("image", selectedFile); // Append file
            formData.append("title", document.querySelector(".titleInput").value);
            formData.append("content", editorContent); // Append TinyMCE content
            formData.append("models", JSON.stringify(selectedModel)); // Convert to JSON
            formData.append("tags", JSON.stringify(selectedTags)); // Convert to JSON

            // Send the request
            fetch(props.fetchUploadUrl, {
                method: 'POST',
                body: formData // Use FormData instead of JSON
            })
                .then(response => response.json())
                .then(data => {
                    console.log("Saved data:", data);
                    window.location.reload();
                })
                .catch(error => console.log("Error:", error));
        }
    };

    const handleLike = (id) => {
        document.querySelector(`#like${id}`).classList.toggle("fa-solid");
        document.querySelector(`#like${id}`).classList.toggle("fa-regular");
        if (document.querySelector(`#like${id}`).classList.contains("fa-solid")) {
            // Update the like count
            document.querySelector(`#likeContainer${id} span`).textContent = parseInt(document.querySelector(`#likeContainer${id} span`).textContent) + 1;

            fetch(props.fetchAddLikeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: id })
            }).then(response => response.json())
                .then(data => {
                    console.log(data);
                })
                .catch(error => console.log(error));
        } else {
            // Update the like count
            document.querySelector(`#likeContainer${id} span`).textContent = parseInt(document.querySelector(`#likeContainer${id} span`).textContent) - 1;

            fetch(props.fetchRemoveLikeUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ id: id })
            }).then(response => response.json())
                .then(data => {
                    console.log(data);
                })
                .catch(error => console.log(error));
        }
    }

    const handleLikePU = (id) => {
        let newIsLiked; // Declare a variable to store the new like state

        setSelectedPublication((prevPublication) => {
            if (!prevPublication) return prevPublication; // Safety check

            newIsLiked = !prevPublication.hasLiked; // Toggle like state

            return {
                ...prevPublication,
                likes: newIsLiked ? prevPublication.likes + 1 : prevPublication.likes - 1,
                hasLiked: newIsLiked
            };
        });

        // Send request to backend using the updated like state
        fetch(newIsLiked ? props.fetchAddLikeUrl : props.fetchRemoveLikeUrl, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id }),
        })
            .then(response => response.json())
            .then(data => console.log(data))
            .catch(error => console.log(error));
    };



    const openPublicationPopUp = (publication) => {
        setSelectedPublication(publication);
    };

    // Close the pop-up
    const closePublicationPopUp = () => {
        setSelectedPublication(null);
    };

    useEffect(() => {
        console.log(newComment);
        if (newComment !== '') {
            fetch(props.fetchAddCommentUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: selectedPublication.id,
                    comment: newComment,
                    isAnswer: newCommentIsAnswer,
                    answerId: answerId
                })
            })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                })
                .catch(error => console.log(error));
            setNewComment('');
            setTemporaryComment('');
            setAnswerId(null);
            setNewCommentIsAnswer(false);
        }
    }, [newComment]);

    useEffect(() => {
        if (getCommentToggle) {
            setAllComments([]);
            console.log(selectedPublication.id);
            fetch(props.fetchGetAllCommentsUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    id: selectedPublication.id,
                })
            })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    // Pour obtenir les commentaires dans l'ordre inverse du plus recent au plus vieux
                    setAllComments(data.comments.reverse());
                })
                .catch(error => console.log(error));
            setGetCommentToggle(false);
        }
    }, [getCommentToggle]);

    const deletePublication = () => {

        fetch(props.fetchDeletePubUrl, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: selectedPublication.id })
        }).then(response => response.json())
            .then(data => {
                console.log(data);
            })
            .catch(error => console.log(error));
    }







    return (
        <div className='sharing'>

            <div className="addPub" onClick={() => setVisiblePopUp(!visiblePopUp)}>
                <i className="fa-solid fa-plus"></i>
            </div>

            {/* Pop-up for adding a publication */}
            {visiblePopUp && (
                <div className="addPubPopUp" >
                    <div className="addPubPopUpContainer">
                        <h2>Ajouter une publication</h2>

                        {/* Upload images */}
                        <h4>Ajouter des photos</h4>
                        <input type="file" className="uploadInput" />

                        {/* Select models */}
                        <h4>Ajouter un modèle (Max: 3)</h4>
                        <select className="modelsSelect" name="modelsSelect">
                            {fetchedModels.length > 0 ? (
                                fetchedModels.map((model) => (
                                    <option key={model.id} value={model.image}>{model.image}</option>
                                ))
                            ) : (
                                <option disabled>Chargement...</option>
                            )}
                        </select>
                        <button className="popUpBtn" onClick={handleModels}>Ajouter</button>

                        {/* Display selected models */}
                        <ul className="modelsList">
                            {selectedModel.map((model, index) => (
                                <li key={index}>
                                    {model}
                                    <span
                                        className="deleteModel"
                                        onClick={() => handleDeleteModel(model)}
                                    >
                                        ❌
                                    </span>
                                </li>
                            ))}
                        </ul>

                        {/* Title */}
                        <h4>Ajouter un titre <span className="requiredFields">*</span></h4>
                        <input type="text" className="titleInput" />

                        {/* Description */}
                        <h4>Ajouter une description <span className="requiredFields">*</span></h4>
                        <Editor
                            apiKey="c8h4io5xkna9j858ivin24vhogp2cbgwwhg08x1udgfwnlif" // Replace with your TinyMCE API key
                            onInit={(evt, editor) => editorRef.current = editor}
                            initialValue=""
                            init={{
                                height: 200,
                                menubar: false,
                                plugins: [
                                    'advlist autolink lists link image charmap print preview anchor',
                                    'searchreplace visualblocks code fullscreen',
                                    'insertdatetime media table paste code help wordcount'
                                ],
                                toolbar:
                                    'undo redo | formatselect | bold italic backcolor | \
                                    alignleft aligncenter alignright alignjustify | \
                                    bullist numlist outdent indent | removeformat | help'
                            }}
                        />

                        {/* Tags */}
                        <h4 style={{ marginTop: '.5rem' }}>Ajouter des tags (Max: 8)</h4>
                        <input type="text" className="tagsInput" />
                        <button className="popUpBtn" onClick={handleTags}>Ajouter</button>

                        {/* Display selected tags */}
                        <ul className="tagsList">
                            {selectedTags.map((tag, index) => (
                                <li key={index}>
                                    <span className="tagSpan">
                                        {tag}
                                    </span>
                                    <span
                                        className="deleteTag"
                                        onClick={() => handleDeleteTag(tag)}
                                    >
                                        ❌
                                    </span>
                                </li>
                            ))}
                        </ul>

                        {/* Publish button */}
                        <div className="publishBtnContainer">
                            <button className="publishBtn" onClick={() => {
                                handleSave();
                                setVisiblePopUp(false);
                            }}>
                                Publier
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Publications Section */}

            {/* Search & Filters Section */}
            <div className="searchContainer">
                <div className="searchAndFilters">
                    <div className="search">
                        <div className="searchBar">
                            <input className="searchInputShare" type="text" placeholder='Rechercher une publication' onChange={(e) => setSearch(e.target.value)} />
                            <i onClick={() => { setSearchPressed(!searchPressed) }} className="fa-solid fa-magnifying-glass"></i>
                        </div>
                        <div className="tagsContainer tagsDisplayNone">
                            {tags.slice(0, 6).map((tag) => (
                                <p className="tag" onClick={() => {
                                    document.querySelector('.searchInputShare').value = tag;
                                    setSearch(tag);
                                }} key={tag}>{tag}</p>
                            ))}
                        </div>
                    </div>
                </div>
            </div>

            <div className="publicationsContainer">
                {allPublications.map((publication) => (
                    <div className="publication" key={publication.id}>
                        <div className="publicationHeader">
                            <div className="pubUserContainer">
                                <div className="pubUserAvatar">
                                    <img src={props.assetUrlProfile + publication.userAvatar} alt="" />
                                </div>
                                <p>{publication.userPseudo}</p>
                            </div>
                            <p>{publication.created_at["date"].split(".")[0]}</p>
                        </div>
                        <div className="carousel-container-publication">
                            <Carousel
                                itemClass='my-carousel-item-publication'
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
                                {publication.images.map((image, idx) => (
                                    <img
                                        key={idx}
                                        src={props.assetUrlSharing + image}
                                        style={{
                                            display: 'block',
                                            maxWidth: '100%',  // Prevents horizontal overflow
                                            maxHeight: '100%', // Prevents vertical overflow
                                            margin: 'auto',
                                            objectFit: 'contain' // Ensures the whole image is visible without distortion
                                        }}
                                        alt=""
                                    />
                                ))}
                                {publication.models.map((model, idx) => (
                                    <img
                                        key={idx}
                                        src={props.assetUrlModels + model["image"]}
                                        style={{
                                            display: 'block',
                                            maxWidth: '100%',  // Prevents horizontal overflow
                                            maxHeight: '100%', // Prevents vertical overflow
                                            margin: 'auto',
                                            objectFit: 'contain' // Ensures the whole image is visible without distortion
                                        }}
                                        alt=""
                                    />
                                ))}
                            </Carousel>
                        </div>
                        <div className="pubContent">
                            <h2>{publication.content["title"]}</h2>
                            <p className="pubParagraph">{publication.content["content"]}</p>
                            <div className="likeAndComments">
                                <div className="commentsContainer" onClick={() => {
                                    openPublicationPopUp(publication);
                                    setTimeout(() => {
                                        setGetCommentToggle(true);
                                    }, 500);
                                }}>
                                    <span>{publication.commentCount}</span>
                                    <span>commentaires</span>
                                    <i class="fa-solid fa-comment"></i>
                                </div>
                                <div id={"likeContainer" + publication.id} className="likeContainer">
                                    <span>{publication.likes}</span>
                                    {publication.hasLiked ? (
                                        <i onClick={() => { handleLike(publication.id) }} id={"like" + publication.id} className="fa-solid fa-heart likeHeart"></i>
                                    ) : (
                                        <i onClick={() => { handleLike(publication.id) }} id={"like" + publication.id} className="fa-regular fa-heart likeHeart"></i>
                                    )}
                                </div>
                            </div>
                        </div>
                        <div className="tags">
                            {publication.tags.map((tag, index) => (
                                <span key={index}>{tag.name}</span>
                            ))}
                        </div>

                        {/* Publication PopUp */}

                        {selectedPublication && (
                            <div className="modalOverlay" onClick={closePublicationPopUp}>

                                <div className="modalContent" onClick={(e) => e.stopPropagation()}>
                                    <div className="publication publicationPU">
                                        <div className="publicationHeader">
                                            <div className="pubUserContainer">
                                                <div className="pubUserAvatar">
                                                    <img src={props.assetUrlProfile + selectedPublication.userAvatar} alt="" />
                                                </div>
                                                <p>{selectedPublication.userPseudo}</p>
                                            </div>
                                            <p>{selectedPublication.created_at["date"].split(".")[0]}</p>
                                        </div>
                                        <div className="carousel-container-publication">
                                            <Carousel
                                                itemClass='my-carousel-item-publication-PU'
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
                                                {selectedPublication.images.map((image, idx) => (
                                                    <img
                                                        key={idx}
                                                        src={props.assetUrlSharing + image}
                                                        style={{
                                                            display: 'block',
                                                            maxWidth: '100%',  // Prevents horizontal overflow
                                                            maxHeight: '100%', // Prevents vertical overflow
                                                            margin: 'auto',
                                                            objectFit: 'contain' // Ensures the whole image is visible without distortion
                                                        }}
                                                        alt=""
                                                    />
                                                ))}
                                                {selectedPublication.models.map((model, idx) => (
                                                    <img
                                                        key={idx}
                                                        src={props.assetUrlModels + model["image"]}
                                                        style={{
                                                            display: 'block',
                                                            maxWidth: '100%',  // Prevents horizontal overflow
                                                            maxHeight: '100%', // Prevents vertical overflow
                                                            margin: 'auto',
                                                            objectFit: 'contain' // Ensures the whole image is visible without distortion
                                                        }}
                                                        alt=""
                                                    />
                                                ))}
                                            </Carousel>
                                        </div>
                                        <div className="pubContent">
                                            <h2>{selectedPublication.content["title"]}</h2>
                                            <p>{selectedPublication.content["content"]}</p>
                                            <div className="likeAndComments">
                                                <div className="commentsContainer">
                                                    <span>{selectedPublication.commentCount}</span>
                                                    <span>commentaires</span>
                                                    <i className="fa-solid fa-comment"></i>
                                                </div>
                                                <div className="likeContainer" id={"likeContainerPU" + selectedPublication.id}>
                                                    <span>{selectedPublication.likes}</span>
                                                    <i
                                                        onClick={() => handleLikePU(selectedPublication.id)}
                                                        id={"likePU" + selectedPublication.id}
                                                        className={selectedPublication.hasLiked ? "fa-solid fa-heart likeHeart" : "fa-regular fa-heart likeHeart"}
                                                    ></i>
                                                </div>
                                            </div>
                                            <hr />
                                            <div className="writeNewComment">
                                                <h3>Ecrire un commentaire :</h3>
                                                <div className="commentInput">
                                                    <input
                                                        className="newCommentInput"
                                                        type="text"
                                                        placeholder="Votre commentaire"
                                                        value={temporaryComment}
                                                        onChange={(e) => setTemporaryComment(e.target.value)}
                                                    />
                                                    <i
                                                        className="fa-solid fa-paper-plane sendNewComment"
                                                        onClick={() => {
                                                            setNewComment(temporaryComment.trim());
                                                            setTimeout(() => {
                                                                setGetCommentToggle(true);
                                                            }, 1000);
                                                        }}
                                                    ></i>
                                                </div>
                                            </div>
                                            <div className="allCommentsContainer">
                                                {allComments.length > 0 ? (
                                                    allComments
                                                        .filter(comment => comment.answerUserID === null)
                                                        .map((comment) => (
                                                            <>
                                                                <div className="comment" key={comment.id}>
                                                                    <div className="commentUserAvatar">
                                                                        <img src={props.assetUrlProfile + comment.userAvatar} alt="" />
                                                                    </div>
                                                                    <div className="commentTextContainer">
                                                                        <div className="commentSpans">
                                                                            <span>{comment.user}</span>
                                                                            <span>{comment.createdAt}</span>
                                                                        </div>
                                                                        <p>{comment.content}</p>
                                                                        <p onClick={() => {
                                                                            setAnswerInputToggle(!answerInputToggle);
                                                                            setAnswerId(comment.id);
                                                                        }}>Répondre</p>
                                                                        {answerInputToggle && answerId === comment.id && (
                                                                            <div className="answerInputContainer">
                                                                                <input
                                                                                    className="newCommentInput"
                                                                                    type="text"
                                                                                    placeholder="Votre commentaire"
                                                                                    value={temporaryComment}
                                                                                    onChange={(e) => setTemporaryComment(e.target.value)}
                                                                                />
                                                                                <i
                                                                                    className="fa-solid fa-paper-plane"
                                                                                    onClick={() => {
                                                                                        setNewComment(temporaryComment.trim());
                                                                                        setTimeout(() => {
                                                                                            setGetCommentToggle(true);
                                                                                        }, 1000);
                                                                                    }}
                                                                                ></i>
                                                                            </div>
                                                                        )}
                                                                    </div>
                                                                </div>
                                                                <div className="subanswers">
                                                                    {allComments
                                                                        .filter(subComment => subComment.answerUserID !== null && subComment.answerUserID === comment.id)
                                                                        .map(subComment => (
                                                                            <div className="subanswer" key={subComment.id}>
                                                                                <div className="commentUserAvatar">
                                                                                    <img src={props.assetUrlProfile + subComment.userAvatar} alt="" />
                                                                                </div>
                                                                                <div className="commentTextContainer">
                                                                                    <div className="commentSpans">
                                                                                        <span>{subComment.user}</span>
                                                                                        <span>{subComment.createdAt}</span>
                                                                                    </div>
                                                                                    <p>{subComment.content}</p>
                                                                                </div>
                                                                            </div>
                                                                        ))}
                                                                </div>
                                                            </>
                                                        ))
                                                ) : (
                                                    <div style={{ textAlign: "center" }}>Aucun commentaires</div>
                                                )}
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                {userPseudo === selectedPublication.userPseudo && (
                                    <button className="deletePubBtn" onClick={deletePublication}>Supprimer la publication</button>
                                )}
                            </div>
                        )}


                    </div>
                ))}
            </div>
        </div>
    );
};

export default Sharing;
