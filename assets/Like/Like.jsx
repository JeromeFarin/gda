import React, { useEffect, useState } from 'react';

export default function Like({ idBook, idUser }) {
    const [liked, setLiked] = useState(false);
    const [likes, setLikes] = useState([]);
    const [token, setToken] = useState('');

    useEffect(() => {
        getLikes();
    }, []);

    async function loadToken() {
        const response = await fetch("/api/login_check", {
            method: "POST",
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
            },
            // TODO: hide login
            body: JSON.stringify({
                username: "admin",
                password: "admin",
            }),
        });
    
        const data = await response.json();
        return data.token;
    }

    async function getLikes() {
        let _token = token;
        if (!_token) {
            _token = await loadToken();
            setToken(_token);
        }
        fetch('/api/likes/book/' + idBook, {
            method: 'GET',
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                Authorization: "Bearer " + _token,
            }
        })
            .then((response) => response.json())
            .then((data) => {
                if (data.find(elt => elt.user.id == idUser)) {
                    setLiked(true);
                } else {
                    setLiked(false);
                }
                setLikes(data);
            });
    }

    const _handleClick = () => {
        fetch('/api/like', {
            method: 'POST',
            headers: {
                Accept: "application/json",
                "Content-Type": "application/json",
                Authorization: "Bearer " + token,
            },
            body: JSON.stringify({
                book: idBook,
                user: idUser
            })
        })
            .then(() => {
                getLikes();
            });
    }

    return (
        <>
            <span
                className="material-icons"
                onClick={_handleClick}
                style={liked ? { color: '#007bff', cursor: 'pointer' } : { cursor: 'pointer' }}
            >
                thumb_up
            </span>
            <em>{likes.length}</em>
        </>
    )
}