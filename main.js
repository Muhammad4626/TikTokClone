document.addEventListener('DOMContentLoaded', () => {
    // Login/Signup form toggling
    const toggleSignup = document.getElementById('toggleSignup');
    const toggleLogin = document.getElementById('showLogin');
    const loginForm = document.getElementById('loginForm');
    const signupForm = document.getElementById('signupForm');
    const loginTitle = document.getElementById('loginTitle');
    const signupTitle = document.getElementById('signupTitle');
    const toggleLoginP = document.getElementById('toggleLogin');

    if (toggleSignup && toggleLogin) {
        toggleSignup.addEventListener('click', (e) => {
            e.preventDefault();
            loginForm.style.display = 'none';
            signupForm.style.display = 'block';
            loginTitle.style.display = 'none';
            signupTitle.style.display = 'block';
            toggleLoginP.style.display = 'block';
            toggleSignup.parentElement.style.display = 'none';
        });

        toggleLogin.addEventListener('click', (e) => {
            e.preventDefault();
            loginForm.style.display = 'block';
            signupForm.style.display = 'none';
            loginTitle.style.display = 'block';
            signupTitle.style.display = 'none';
            toggleLoginP.style.display = 'none';
            toggleSignup.parentElement.style.display = 'block';
        });
    }

    // Video auto-play on scroll
    const videos = document.querySelectorAll('.video-container video, .videos-container video');
    const videoFeed = document.querySelector('.video-feed');
    if (videoFeed && videos.length > 0) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                const video = entry.target;
                const playBtn = video.nextElementSibling;
                if (entry.isIntersecting) {
                    video.play().then(() => {
                        if (playBtn && playBtn.classList.contains('play-btn')) {
                            playBtn.style.display = 'none';
                        }
                    }).catch(err => {
                        console.error('Video play error:', err, 'Video src:', video.currentSrc);
                        if (playBtn && playBtn.classList.contains('play-btn')) {
                            playBtn.style.display = 'block';
                        }
                    });
                } else {
                    video.pause();
                    video.currentTime = 0; // Reset to start
                    if (playBtn && playBtn.classList.contains('play-btn')) {
                        playBtn.style.display = 'block';
                    }
                }
            });
        }, { threshold: 0.6 });

        videos.forEach(video => {
            observer.observe(video);
            video.addEventListener('error', (e) => {
                console.error('Video load error:', e, 'Video src:', video.currentSrc);
                const playBtn = video.nextElementSibling;
                if (playBtn && playBtn.classList.contains('play-btn')) {
                    playBtn.style.display = 'block';
                }
            });
            video.addEventListener('loadeddata', () => {
                console.log('Video loaded:', video.currentSrc);
            });
        });

        // Play button click handler
        document.querySelectorAll('.play-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const video = btn.previousElementSibling;
                video.play().then(() => {
                    btn.style.display = 'none';
                }).catch(err => {
                    console.error('Manual play error:', err);
                });
            });
        });
    } else {
        console.warn('No videos or video-feed found on page');
    }

    // Edit Profile modal
    const editProfileBtn = document.getElementById('editProfileBtn');
    const editProfileModal = document.getElementById('editProfileModal');
    const closeEditProfile = document.getElementById('closeEditProfile');
    if (editProfileBtn && editProfileModal && closeEditProfile) {
        editProfileBtn.addEventListener('click', () => {
            editProfileModal.style.display = 'block';
        });
        closeEditProfile.addEventListener('click', () => {
            editProfileModal.style.display = 'none';
        });
        window.addEventListener('click', (e) => {
            if (e.target === editProfileModal) {
                editProfileModal.style.display = 'none';
            }
        });
    }

    // Comment modal
    const commentModal = document.getElementById('commentModal');
    const closeComment = document.getElementById('closeComment');
    const commentForm = document.getElementById('commentForm');
    const commentList = document.getElementById('commentList');
    const commentVideoId = document.getElementById('commentVideoId');
    if (commentModal && closeComment && commentForm) {
        document.querySelectorAll('.comment-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const videoId = btn.getAttribute('data-video-id');
                commentVideoId.value = videoId;
                commentModal.style.display = 'block';
                fetchComments(videoId);
            });
        });
        closeComment.addEventListener('click', () => {
            commentModal.style.display = 'none';
        });
        window.addEventListener('click', (e) => {
            if (e.target === commentModal) {
                commentModal.style.display = 'none';
            }
        });
        commentForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(commentForm);
            fetch('comment_video.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const btn = document.querySelector(`.comment-btn[data-video-id="${formData.get('video_id')}"]`);
                    btn.querySelector('span').textContent = data.count;
                    renderComments(data.comments);
                    commentForm.reset();
                } else {
                    alert(data.message);
                }
            });
        });
    }

    function fetchComments(videoId) {
        fetch('comment_video.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `video_id=${videoId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderComments(data.comments);
            }
        });
    }

    function renderComments(comments) {
        commentList.innerHTML = comments.map(c => `<div class="comment-item"><span>${c.username}</span>: ${c.comment}</div>`).join('');
    }

    // Like buttons
    document.querySelectorAll('.like-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const videoId = btn.getAttribute('data-video-id');
            fetch('like_video.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `video_id=${videoId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    btn.querySelector('span').textContent = data.count;
                } else {
                    alert(data.message);
                }
            });
        });
    });

    // Follow buttons
    document.querySelectorAll('.follow-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const userId = btn.getAttribute('data-user-id');
            fetch('add_friend.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: `followed_id=${userId}`
            })
            .then(response => response.text())
            .then(() => {
                btn.textContent = 'Following';
                btn.disabled = true;
            })
            .catch(err => console.log('Follow error:', err));
        });
    });

    // Friend search
    const friendSearch = document.getElementById('friendSearch');
    const suggestedUsers = document.querySelectorAll('.suggested-users ul li');
    if (friendSearch && suggestedUsers) {
        friendSearch.addEventListener('input', () => {
            const search = friendSearch.value.toLowerCase();
            suggestedUsers.forEach(user => {
                const username = user.querySelector('span').textContent.toLowerCase();
                user.style.display = username.includes(search) ? 'flex' : 'none';
            });
        });
    }

    // Message modal
    const messageModal = document.getElementById('messageModal');
    const closeMessage = document.getElementById('closeMessage');
    const messageForm = document.getElementById('messageForm');
    const messageList = document.getElementById('messageList');
    const receiverId = document.getElementById('receiverId');
    if (messageModal && closeMessage && messageForm) {
        document.querySelectorAll('.conversation').forEach(conv => {
            conv.addEventListener('click', () => {
                const userId = conv.getAttribute('data-user-id');
                receiverId.value = userId;
                messageModal.style.display = 'block';
                fetchMessages(userId);
            });
        });
        closeMessage.addEventListener('click', () => {
            messageModal.style.display = 'none';
        });
        window.addEventListener('click', (e) => {
            if (e.target === messageModal) {
                messageModal.style.display = 'none';
            }
        });
        messageForm.addEventListener('submit', (e) => {
            e.preventDefault();
            const formData = new FormData(messageForm);
            fetch('send_message.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderMessages(data.messages);
                    messageForm.reset();
                } else {
                    alert(data.message);
                }
            });
        });
    }

    function fetchMessages(userId) {
        fetch('send_message.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `receiver_id=${userId}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                renderMessages(data.messages);
            }
        });
    }

    function renderMessages(messages) {
        messageList.innerHTML = messages.map(m => `<div class="message-item"><span>${m.username}</span>: ${m.message}</div>`).join('');
    }

    // Upload modal
    const uploadBtn = document.getElementById('uploadBtn');
    const uploadModal = document.getElementById('uploadModal');
    const closeUploadBtn = document.getElementById('closeUpload');
    if (uploadBtn && uploadModal && closeUploadBtn) {
        uploadBtn.addEventListener('click', () => {
            uploadModal.style.display = 'block';
        });
        closeUploadBtn.addEventListener('click', () => {
            uploadModal.style.display = 'none';
        });
        window.addEventListener('click', (e) => {
            if (e.target === uploadModal) {
                uploadModal.style.display = 'none';
            }
        });
    }
});
document.addEventListener('DOMContentLoaded', () => {
    // Handle follow buttons
    document.querySelectorAll('.follow-btn').forEach(button => {
        button.addEventListener('click', () => {
            const userId = button.dataset.userId;
            fetch('follow.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `followed_id=${userId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    button.textContent = 'Unfollow';
                    button.classList.remove('follow-btn');
                    button.classList.add('unfollow-btn');
                    alert(data.message); // Replace with better UI feedback
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Follow error:', error);
                alert('Failed to follow user.');
            });
        });
    });

    // Handle unfollow buttons
    document.querySelectorAll('.unfollow-btn').forEach(button => {
        button.addEventListener('click', () => {
            const userId = button.dataset.userId;
            fetch('unfollow.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `followed_id=${userId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    button.textContent = 'Follow';
                    button.classList.remove('unfollow-btn');
                    button.classList.add('follow-btn');
                    alert(data.message); // Replace with better UI feedback
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Unfollow error:', error);
                alert('Failed to unfollow user.');
            });
        });
    });
});