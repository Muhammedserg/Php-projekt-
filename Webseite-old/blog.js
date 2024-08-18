
const container = document.getElementById("blogpost-container");

// Lade die Blogpost-Daten Aus JSON-Datei (dummy.json)
fetch("dummy.json")
    .then(response => response.json())
    .then(blogposts => {
        // Iteriere durch die Blogposts und erstelle Kärtchen
        blogposts.forEach(blogpost => {
            const blogpostCard = document.createElement("div");
            blogpostCard.classList.add("blogpost");

            const img = document.createElement("img");
            img.src = blogpost.img;
            img.alt = blogpost.title;

            const title = document.createElement("h2");
            title.textContent = blogpost.title;

            const text = document.createElement("div");
            text.classList.add("blogpost-text");
            text.textContent = blogpost.text.substring(0, 150); //  den Text auf 150 Zeichen
            

            blogpostCard.appendChild(img);
            blogpostCard.appendChild(title);
            blogpostCard.appendChild(text);

            container.appendChild(blogpostCard);

        });
    })
    .catch(error => console.error("Fehler beim Laden der Blogpost-Daten: " + error));