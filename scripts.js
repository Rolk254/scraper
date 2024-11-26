document.addEventListener('DOMContentLoaded', () => {
    const episodes = document.querySelectorAll('.episode');
    const prevBtn = document.querySelector('.nav-btn.prev');
    const nextBtn = document.querySelector('.nav-btn.next');
    const loadingContainer = document.querySelector('.loading-container');
    const episodeGrid = document.querySelector('.episode-grid');

    function captureThumbnail(videoSrc, imgElement, thumbnailTime) {
        return new Promise((resolve, reject) => {
            const video = document.createElement('video');
            video.src = videoSrc;
            video.crossOrigin = 'anonymous';
            video.addEventListener('loadeddata', () => {
                video.currentTime = thumbnailTime;
            });
            video.addEventListener('seeked', () => {
                const canvas = document.createElement('canvas');
                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                imgElement.src = canvas.toDataURL('image/jpeg');
                resolve();
            });
            video.addEventListener('error', reject);
        });
    }

    function showContent() {
        loadingContainer.style.display = 'none';
        episodeGrid.style.display = 'grid';
    }

    let imagesLoaded = 0;
    const totalEpisodes = episodes.length;

    Promise.all(Array.from(episodes).map((episode) => {
        const img = episode.querySelector('img:not(.watched-overlay)');
        const videoSrc = episode.dataset.videoSrc;
        const thumbnailTime = parseFloat(episode.dataset.thumbnailTime);
        return captureThumbnail(videoSrc, img, thumbnailTime)
            .then(() => {
                imagesLoaded++;
                if (imagesLoaded === totalEpisodes) {
                    showContent();
                }
            });
    }));

    function savePlaybackTime(userId, episodeNumber, currentTime) {
        return fetch('save_playback_time.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                user_id: userId,
                episode_number: episodeNumber,
                timecurrent: currentTime
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.text();
        })
        .then(result => {
            console.log("Respuesta del servidor:", result);
        })
        .catch(error => console.error("Error al guardar el tiempo de reproducción:", error));
    }

    function updateEpisodeStatus(episodeNumber, watched) {
        const userId = 1; // Aquí deberías obtener el ID del usuario de alguna manera
        return fetch('update_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: `user_id=${userId}&episode_number=${episodeNumber}&watched=${watched}`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al actualizar el estado del episodio.');
            }
        });
    }

    function getSavedPlaybackTime(userId, episodeNumber) {
        return fetch('get_playback_time.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: new URLSearchParams({
                user_id: userId,
                episode_number: episodeNumber
            })
        })
        .then(response => response.json())
        .then(data => {
            return data.timecurrent || 0; // Devuelve 0 si no se encuentra el tiempo
        })
        .catch(error => {
            console.error("Error al obtener el tiempo de reproducción:", error);
            return 0; // Devuelve 0 en caso de error
        });
    }

    function closeCurrentPopup() {
        const currentPopup = document.querySelector('.episode-popup.active');
        if (currentPopup) {
            const video = currentPopup.querySelector('video');
            if (video) {
                video.pause();
            }
            currentPopup.classList.remove('active');
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'none';
        }
    }

    function openPopup(episode) {
        const img = episode.querySelector('img:not(.watched-overlay)');
        const videoSrc = episode.dataset.videoSrc;
        const popup = episode.querySelector('.episode-popup');
        const videoPopup = popup.querySelector('video');
        const userId = 1; // Aquí deberías obtener el ID del usuario de alguna manera

        popup.classList.add('active');
        videoPopup.src = videoSrc;

        // Obtener el tiempo guardado del servidor
        getSavedPlaybackTime(userId, episode.dataset.episodeNumber)
            .then(savedTime => {
                videoPopup.addEventListener('loadedmetadata', () => {
                    videoPopup.currentTime = savedTime;
                    videoPopup.play();
                }, { once: true });
            });

        prevBtn.style.display = 'block';
        nextBtn.style.display = 'block';
    }

    episodes.forEach((episode, index) => {
        const img = episode.querySelector('img:not(.watched-overlay)');
        const checkbox = episode.querySelector('.episode-checkbox');
        const overlay = episode.querySelector('.watched-overlay');
        const episodeNumber = episode.dataset.episodeNumber;
        const popup = episode.querySelector('.episode-popup');
        const closeBtn = popup.querySelector('.close-btn');
        const videoPopup = popup.querySelector('video');
        const seekBackwardBtn = popup.querySelector('.seek-backward');
        const seekForwardBtn = popup.querySelector('.seek-forward');
        const userId = 1; // Aquí deberías obtener el ID del usuario de alguna manera

        img.addEventListener('click', () => {
            closeCurrentPopup();
            openPopup(episode);
        });

        closeBtn.addEventListener('click', () => {
            closeCurrentPopup();
        });

        popup.addEventListener('click', (e) => {
            if (e.target === popup) {
                closeCurrentPopup();
            }
        });

        // Guardar el tiempo de reproducción al pausar el video
        videoPopup.addEventListener('pause', () => {
            const currentTime = videoPopup.currentTime;
            savePlaybackTime(userId, episodeNumber, currentTime);
        });

        // Mostrar el temporizador cuando el video termina
        videoPopup.addEventListener('ended', () => {
            const timerOverlay = document.createElement('div');
            timerOverlay.classList.add('timer-overlay');
            popup.appendChild(timerOverlay);

            const timerContainer = document.createElement('div');
            timerContainer.classList.add('timer-container');

            const progressRing = document.createElement('div');
            progressRing.classList.add('progress-ring');
            progressRing.style.setProperty('--progress', '0%');

            const timerText = document.createElement('div');
            timerText.classList.add('timer-text');

            timerContainer.appendChild(progressRing);
            timerContainer.appendChild(timerText);
            timerOverlay.appendChild(timerContainer);

            let countdown = 5;
            timerText.innerText = countdown;
            timerOverlay.classList.add('show');

            progressRing.style.animation = `countdownAnimation ${countdown}s linear forwards`;

            const countdownInterval = setInterval(() => {
                countdown--;
                timerText.innerText = countdown;

                if (countdown <= 0) {
                    clearInterval(countdownInterval);
                    timerOverlay.classList.remove('show');
                    popup.classList.remove('active');
                    videoPopup.pause();

                    if (index < totalEpisodes - 1) {
                        episodes[index + 1].querySelector('img').click(); // Reproduce el siguiente episodio
                    }

                    setTimeout(() => {
                        popup.removeChild(timerOverlay);
                    }, 300);
                }
            }, 1000);
        });

        seekBackwardBtn.addEventListener('click', () => {
            if (videoPopup) {
                videoPopup.currentTime = Math.max(videoPopup.currentTime - 5, 0); // Retroceder 5 segundos, sin ir más allá de 0
            }
        });

        seekForwardBtn.addEventListener('click', () => {
            if (videoPopup) {
                videoPopup.currentTime = Math.min(videoPopup.currentTime + 5, videoPopup.duration); // Avanzar 5 segundos, sin exceder la duración del video
            }
        });

        checkbox.addEventListener('change', () => {
            const watched = checkbox.checked;
            updateEpisodeStatus(episodeNumber, watched)
                .then(() => {
                    overlay.style.display = watched ? 'block' : 'none';
                });
        });
    });

    prevBtn.addEventListener('click', () => {
        const currentEpisode = document.querySelector('.episode-popup.active');
        if (currentEpisode) {
            const currentIndex = Array.from(episodes).indexOf(currentEpisode.closest('.episode'));
            if (currentIndex > 0) {
                episodes[currentIndex - 1].querySelector('img').click();
            }
        }
    });

    nextBtn.addEventListener('click', () => {
        const currentEpisode = document.querySelector('.episode-popup.active');
        if (currentEpisode) {
            const currentIndex = Array.from(episodes).indexOf(currentEpisode.closest('.episode'));
            if (currentIndex < totalEpisodes - 1) {
                episodes[currentIndex + 1].querySelector('img').click();
            }
        }
    });
});
