console.log("📖");

import MagicGrid from "magic-grid";
let newspaperGrid = document.querySelectorAll(".newspaper-grid ul li");

// Fonction pour ajouter des images aléatoires dans la grille
// function addRandomImages() {
//     const gridContainer = document.querySelector(".newspaper-grid .grid-items");
//     if (!gridContainer) return;

//     // Liste des images disponibles
//     const images = [
//         'Ampoule.png',
//         'app-photo.png', 
//         'Jumelles.png',
//         'machine.png',
//         'Main.png',
//         'mongolfiere.png',
//         'photographe.png',
//         'Plume.png'
//     ];

//     // Chemin vers le dossier des images
//     const imagePath = '/wp-content/themes/infokiosque/src/images/1x/';

//     // Nombre d'images à insérer (ajustable)
//     const numberOfImages = Math.floor(Math.random() * 4) + 2; // Entre 2 et 5 images

//     // Obtenir tous les éléments existants dans la grille
//     const existingItems = gridContainer.querySelectorAll('.wp-block-post');
//     const totalItems = existingItems.length;

//     if (totalItems === 0) return;

//     // Créer une copie du tableau d'images pour éviter les doublons
//     const availableImages = [...images];
    
//     // Créer et insérer les images à des positions aléatoires
//     for (let i = 0; i < numberOfImages && availableImages.length > 0; i++) {
//         // Sélectionner une image aléatoire et la retirer de la liste
//         const randomIndex = Math.floor(Math.random() * availableImages.length);
//         const selectedImage = availableImages.splice(randomIndex, 1)[0];
        
//         // Créer l'élément div avec l'image
//         const visuWrapper = document.createElement('div');
//         visuWrapper.className = 'visu-wrapper';
//         visuWrapper.innerHTML = `<img src="${imagePath}${selectedImage}" alt="Illustration" loading="lazy">`;

//         // Position aléatoire dans la grille (entre les articles existants)
//         const randomPosition = Math.floor(Math.random() * (totalItems + 1));
        
//         if (randomPosition >= totalItems) {
//             gridContainer.appendChild(visuWrapper);
//         } else {
//             gridContainer.insertBefore(visuWrapper, existingItems[randomPosition]);
//         }
//     }

//     // Relancer MagicGrid pour prendre en compte les nouveaux éléments
//     setTimeout(() => {
//         magicGrid.positionItems();
//     }, 200);
// }

// // Attendre que le DOM soit chargé puis ajouter les images
// document.addEventListener('DOMContentLoaded', () => {
//     setTimeout(addRandomImages, 100); // Petit délai pour s'assurer que la grille est initialisée
// });

// let magicGrid = new MagicGrid({
//     container: ".newspaper-grid .grid-items",
//     static: true, // Required for static content.
//     gutter: 0,
//     useMin: true,
//     useTransform: true, // Optional. Position items using CSS transform? Default: True.
//     // center: true, //Optional. Center the grid items? Default: true.
//     maxColumns: 6,
//     animate: true,
//     items:15
// });

// // const id = magicGrid.onPositionComplete(() => {
// //   console.log("Grid Has Been Resized"); // Example function
// // });

// magicGrid.listen();


