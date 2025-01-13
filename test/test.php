<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gotjung Hotel</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <style>
        .carousel-inner img {
            width: 100%;
            height: 100%;
        }
        .carousel-indicators button {
            background-color: #fff;
            border: none;
            width: 12px;
            height: 12px;   
            border-radius: 50%;
            margin: 0 2px;
            cursor: pointer;
        }
        .carousel-indicators button.active {
            background-color: #000;
        }
    </style>
</head>
<body class="bg-gray-100">

<div class="container mx-auto mt-5">
    <h2 class="text-2xl font-bold mb-5">Welcome to Gotjung Hotel</h2>
    <div id="hotelCarousel" class="relative">
        <div class="carousel-inner relative w-full overflow-hidden">
            <div class="carousel-item active relative float-left w-full transition duration-500 ease-in-out transform">
                <img src="https://via.placeholder.com/800x400" alt="Slide 1" class="block w-full">
                <div class="carousel-caption absolute text-center bottom-0 bg-gray-800 bg-opacity-50 text-white p-2">
                    <h3 class="text-xl">Slide 1</h3>
                    <p>Description for Slide 1</p>
                </div>
            </div>
            <div class="carousel-item relative float-left w-full transition duration-500 ease-in-out transform">
                <img src="https://via.placeholder.com/800x400" alt="Slide 2" class="block w-full">
                <div class="carousel-caption absolute text-center bottom-0 bg-gray-800 bg-opacity-50 text-white p-2">
                    <h3 class="text-xl">Slide 2</h3>
                    <p>Description for Slide 2</p>
                </div>
            </div>
            <div class="carousel-item relative float-left w-full transition duration-500 ease-in-out transform">
                <img src="https://via.placeholder.com/800x400" alt="Slide 3" class="block w-full">
                <div class="carousel-caption absolute text-center bottom-0 bg-gray-800 bg-opacity-50 text-white p-2">
                    <h3 class="text-xl">Slide 3</h3>
                    <p>Description for Slide 3</p>
                </div>
            </div>
        </div>
        <div class="carousel-indicators absolute bottom-0 left-0 right-0 flex justify-center p-0 mb-4">
            <button type="button" data-target="#hotelCarousel" data-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
            <button type="button" data-target="#hotelCarousel" data-slide-to="1" aria-label="Slide 2"></button>
            <button type="button" data-target="#hotelCarousel" data-slide-to="2" aria-label="Slide 3"></button>
        </div>
        <a class="carousel-control-prev absolute top-0 bottom-0 flex items-center justify-center p-0 text-center border-0 hover:outline-none hover:no-underline focus:outline-none focus:no-underline left-0" href="#hotelCarousel" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon inline-block bg-no-repeat" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next absolute top-0 bottom-0 flex items-center justify-center p-0 text-center border-0 hover:outline-none hover:no-underline focus:outline-none focus:no-underline right-0" href="#hotelCarousel" role="button" data-slide="next">
            <span class="carousel-control-next-icon inline-block bg-no-repeat" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@2.8.2/dist/alpine.min.js" defer></script>
</body>
</html>
