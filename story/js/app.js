document.addEventListener('DOMContentLoaded', () => {
    const API_BASE_URL = 'http://localhost/story_app/backend/api'; // Adjust if your local server is different
    const storyGrid = document.getElementById('story-grid');
    const storyViewer = document.getElementById('story-viewer');
    const storyContent = document.getElementById('story-content');
    const closeViewerBtn = document.getElementById('close-viewer');
    const languageFilter = document.getElementById('language-filter');
    const categoryFilter = document.getElementById('category-filter');

    const fetchLanguages = async () => {
        try {
            const response = await fetch(`${API_BASE_URL}/languages.php`);
            const languages = await response.json();
            languages.forEach(lang => {
                const option = document.createElement('option');
                option.value = lang.id;
                option.textContent = lang.name;
                languageFilter.appendChild(option);
            });
        } catch (error) {
            console.error('Error fetching languages:', error);
        }
    };

    const fetchCategories = async () => {
        try {
            const response = await fetch(`${API_BASE_URL}/categories.php`);
            const categories = await response.json();
            categories.forEach(cat => {
                const option = document.createElement('option');
                option.value = cat.id;
                option.textContent = cat.name;
                categoryFilter.appendChild(option);
            });
        } catch (error) {
            console.error('Error fetching categories:', error);
        }
    };

    const fetchStories = async () => {
        const langId = languageFilter.value;
        const catId = categoryFilter.value;
        let url = `${API_BASE_URL}/stories.php?`;
        if (langId) url += `language_id=${langId}&`;
        if (catId) url += `category_id=${catId}`;

        try {
            const response = await fetch(url);
            const stories = await response.json();
            displayStories(stories);
        } catch (error) {
            console.error('Error fetching stories:', error);
            storyGrid.innerHTML = '<p>Could not load stories. Please try again later.</p>';
        }
    };

    const displayStories = (stories) => {
        storyGrid.innerHTML = '';
        if (stories.length === 0) {
            storyGrid.innerHTML = '<p>No stories found for this selection.</p>';
            return;
        }
        stories.forEach(story => {
            const card = document.createElement('div');
            card.className = 'story-card';
            card.innerHTML = `
                <img src="${story.cover_image.replace('http://localhost', 'http://127.0.0.1')}" alt="${story.title}">
                <h3>${story.title}</h3>
            `; // Note: Replacing localhost with 127.0.0.1 can help avoid some browser security issues.
            card.addEventListener('click', () => showStoryDetails(story.id));
            storyGrid.appendChild(card);
        });
    };

    const showStoryDetails = async (storyId) => {
        try {
            const response = await fetch(`${API_BASE_URL}/story_details.php?id=${storyId}`);
            const story = await response.json();
            storyContent.innerHTML = `
                <h2>${story.title}</h2>
                <img src="${story.cover_image.replace('http://localhost', 'http://127.0.0.1')}" alt="${story.title}">
                ${story.pages.map(page => `
                    <div>
                        ${page.image ? `<img src="${page.image.replace('http://localhost', 'http://127.0.0.1')}" alt="Page ${page.page_number}">` : ''}
                        <p>${page.text.replace(/\n/g, '<br>')}</p>
                    </div>
                `).join('<hr>')}
            `;
            storyViewer.classList.remove('hidden');
        } catch (error) {
            console.error('Error fetching story details:', error);
        }
    };

    closeViewerBtn.addEventListener('click', () => {
        storyViewer.classList.add('hidden');
        storyContent.innerHTML = '';
    });

    languageFilter.addEventListener('change', fetchStories);
    categoryFilter.addEventListener('change', fetchStories);

    // Initial Load
    fetchLanguages();
    fetchCategories();
    fetchStories();

    // Register Service Worker for PWA
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/service-worker.js')
                .then(registration => {
                    console.log('ServiceWorker registration successful with scope: ', registration.scope);
                })
                .catch(err => {
                    console.log('ServiceWorker registration failed: ', err);
                });
        });
    }
});