document.addEventListener('DOMContentLoaded', () => {
    const loadCars = (containerId, sortOption = 'default') => {
        let sortQuery = '';
        if (sortOption === 'priceAsc') {
            sortQuery = 'priceAsc';
        } else if (sortOption === 'priceDesc') {
            sortQuery = 'priceDesc';
        }

        console.log('Sortowanie: ', sortQuery);

        fetch(`fetch_cars.php?sort=${sortQuery}`)
            .then(response => response.json())
            .then(data => {
                console.log('Otrzymane dane: ', data);
                const carsContainer = document.getElementById(containerId);
                if (!carsContainer) {
                    console.error(`Nie znaleziono kontenera o ID: ${containerId}`);
                    return;
                }
                carsContainer.innerHTML = '';
                data.forEach(car => {
                    const carItem = document.createElement('div');
                    carItem.className = 'car-item';
                    carItem.innerHTML = `
                        <div class="car-image" style="background-image: url('${car.image}');"></div>
                        <h3>${car.model}</h3>
                        <p>Cena: ${car.price} PLN</p>
                        <p>Pojemność: ${car.capacity}</p>
                        <p>Typ paliwa: ${car.fuel_type}</p>
                        ${containerId === 'admin-cars-container' ? `
                            <button class="edit-car" data-id="${car.id}">Edytuj</button>
                            <br>
                            <button class="delete-car" data-id="${car.id}">Usuń</button>
                        ` : `
                            <button class="add-to-cart" data-id="${car.id}" data-model="${car.model}" data-price="${car.price}" data-image="${car.image}">Dodaj do koszyka</button>
                        `}
                    `;
                    carsContainer.appendChild(carItem);
                });

                // Nasłuchiwanie przycisków edytuj i usuń
                if (containerId === 'admin-cars-container') {
                    const editButtons = document.querySelectorAll('.edit-car');
                    if (editButtons.length === 0) {
                        console.warn('Brak przycisków edytuj.');
                    }
                    editButtons.forEach(button => {
                        button.addEventListener('click', function() {
                            const carId = this.dataset.id;
                            window.location.href = `edit_car.html?id=${carId}`;
                        });
                    });

                    const deleteButtons = document.querySelectorAll('.delete-car');
                    if (deleteButtons.length === 0) {
                        console.warn('Brak przycisków usuń.');
                    }
                    deleteButtons.forEach(button => {
                        button.addEventListener('click', function() {
                            const carId = this.dataset.id;
                            if (confirm('Czy na pewno chcesz usunąć ten model?')) {
                                deleteCar(carId);
                            }
                        });
                    });
                } else {
                    const addToCartButtons = document.querySelectorAll('.add-to-cart');
                    if (addToCartButtons.length === 0) {
                        console.warn('Brak przycisków dodaj do koszyka.');
                    }
                    addToCartButtons.forEach(button => {
                        button.addEventListener('click', function() {
                            addToCart(this.dataset.id, this.dataset.model, this.dataset.price, this.dataset.image);
                        });
                    });
                }
            })
            .catch(error => {
                console.error('Błąd:', error);
            });
    };

    const addToCart = (id, model, price, image) => {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        const itemIndex = cart.findIndex(item => item.id === id);
        if (itemIndex === -1) {
            cart.push({ id, model, price, image, quantity: 1 });
        } else {
            cart[itemIndex].quantity += 1;
        }
        localStorage.setItem('cart', JSON.stringify(cart));
        alert('Samochód dodany do koszyka!');
    };

    const removeFromCart = (id) => {
        let cart = JSON.parse(localStorage.getItem('cart')) || [];
        cart = cart.filter(item => item.id !== id);
        localStorage.setItem('cart', JSON.stringify(cart));
        alert('Samochód usunięty z koszyka!');
        location.reload();
    };

    const loadCart = () => {
        const cartContainer = document.getElementById('cart-container');
        if (!cartContainer) {
            console.error('Nie znaleziono kontenera koszyka.');
            return;
        }
        
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
    
        if (cart.length === 0) {
            cartContainer.innerHTML = '<p>Twój koszyk jest pusty.</p>';
            return;
        }
    
        cart.forEach(item => {
            const cartItem = document.createElement('div');
            cartItem.className = 'car-item';
            cartItem.innerHTML = `
                <div class="car-image" style="background-image: url('${item.image}');"></div>
                <h3>${item.model}</h3>
                <p>Cena: ${item.price} PLN</p>
                <p>Ilość: ${item.quantity}</p>
                <button class="remove-from-cart" data-id="${item.id}">Usuń</button>
            `;
            cartContainer.appendChild(cartItem);
        });
    
        const removeFromCartButtons = document.querySelectorAll('.remove-from-cart');
        if (removeFromCartButtons.length === 0) {
            console.warn('Brak przycisków usuń z koszyka.');
        }
        removeFromCartButtons.forEach(button => {
            button.addEventListener('click', function() {
                removeFromCart(this.dataset.id);
            });
        });
    
        const orderButton = document.getElementById('submitOrderButton');
        if (orderButton) {
            orderButton.addEventListener('click', () => {
                const totalPrice = calculateTotalPrice();
                if (totalPrice > 0) {
                    fetch('place_order.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `total_price=${totalPrice}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert('Zamówienie zostało złożone.');
                            localStorage.removeItem('cart');
                            location.reload();
                        } else {
                            alert('Wystąpił błąd podczas składania zamówienia.');
                        }
                    })
                    .catch(error => {
                        console.error('Błąd:', error);
                        alert('Wystąpił błąd podczas składania zamówienia.');
                    });
                } else {
                    alert('Koszyk jest pusty.');
                }
            });
        }
    };

    const calculateTotalPrice = () => {
        const cart = JSON.parse(localStorage.getItem('cart')) || [];
        return cart.reduce((total, item) => total + item.price * item.quantity, 0);
    };

    const deleteCar = (carId) => {
        fetch('delete_car.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `id=${carId}`
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Problem z serwerem');
            }
            return response.text();
        })
        .then(text => {
            console.log('Odpowiedź z serwera:', text);
            try {
                const data = JSON.parse(text);
                if (data.success) {
                    alert('Samochód został usunięty.');
                    loadCars('admin-cars-container');
                } else {
                    alert('Wystąpił błąd podczas usuwania samochodu: ' + data.message);
                }
            } catch (e) {
                console.error('Błąd parsowania JSON:', e);
                alert('Wystąpił błąd podczas usuwania samochodu.');
            }
        })
        .catch(error => {
            console.error('Błąd:', error);
            alert('Wystąpił błąd podczas usuwania samochodu.');
        });
    };

    const loadCarData = () => {
        const urlParams = new URLSearchParams(window.location.search);
        const carId = urlParams.get('id');

        if (carId) {
            fetch(`get_car.php?id=${carId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('editModel').value = data.car.model;
                        document.getElementById('editPrice').value = data.car.price;
                        document.getElementById('editCapacity').value = data.car.capacity;
                        document.getElementById('editFuelType').value = data.car.fuel_type;
                    } else {
                        document.getElementById('statusMessage').textContent = "Nie znaleziono danych samochodu.";
                    }
                })
                .catch(error => {
                    console.error('Błąd:', error);
                    document.getElementById('statusMessage').textContent = "Wystąpił błąd podczas ładowania danych.";
                });
        } else {
            document.getElementById('statusMessage').textContent = "Brak identyfikatora samochodu.";
        }
    };

    const updateCar = () => {
        const urlParams = new URLSearchParams(window.location.search);
        const carId = urlParams.get('id');

        if (carId) {
            const model = document.getElementById('editModel').value;
            const price = document.getElementById('editPrice').value;
            const capacity = document.getElementById('editCapacity').value;
            const fuelType = document.getElementById('editFuelType').value;

            fetch('update_car.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `id=${carId}&model=${model}&price=${price}&capacity=${capacity}&fuel_type=${fuelType}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Samochód został zaktualizowany.');
                    window.location.href = 'admin.html';
                } else {
                    alert('Wystąpił błąd podczas aktualizacji samochodu: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Błąd:', error);
                alert('Wystąpił błąd podczas aktualizacji samochodu.');
            });
        } else {
            alert('Brak identyfikatora samochodu.');
        }
    };

    document.getElementById('sortOptions')?.addEventListener('change', (event) => {
        const sortOption = event.target.value;
        loadCars('cars-container', sortOption);
    });

    // Inicjalne ładowanie samochodów bez sortowania
    if (document.getElementById('cars-container') && window.location.pathname.includes('all_models.html')) {
        loadCars('cars-container');
    }

    const adminCarsContainer = document.getElementById('admin-cars-container');
    if (adminCarsContainer) {
        loadCars('admin-cars-container');
    }

    if (window.location.pathname.includes('cart.html')) {
        loadCart();
    }

    if (window.location.pathname.includes('edit_car.html')) {
        loadCarData();

        // Nasłuchiwanie na zdarzenie kliknięcia przycisku aktualizacji w formularzu edycji
        const updateButton = document.getElementById('update-car-button');
        if (updateButton) {
            updateButton.addEventListener('click', updateCar);
        }
    }

    function searchCar() {
        var searchModelElement = document.getElementById("searchModel");
        if (!searchModelElement) {
            alert("Element searchModel nie został znaleziony.");
            return;
        }

        var model = searchModelElement.value;
        if (model === "") {
            alert("Proszę wpisać nazwę modelu.");
            return;
        }

        fetch(`search_car.php?model=${model}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById("search-result").innerHTML = 
                        `<p>Znaleziono model: ${data.car.model}</p>`;
                } else {
                    document.getElementById("search-result").innerHTML = 
                        `<p>Nie znaleziono samochodu o tej nazwie.</p>`;
                }
            })
            .catch(error => {
                console.error('Błąd:', error);
                document.getElementById("search-result").innerHTML = 
                    `<p>Wystąpił błąd podczas wyszukiwania.</p>`;
            });
    }
});
function searchCar() {
    var searchModelElement = document.getElementById("searchModel");
    console.log(searchModelElement); // Sprawdź, czy element został znaleziony

    if (!searchModelElement) {
        alert("Element searchModel nie został znaleziony.");
        return;
    }

    var model = searchModelElement.value; // Pobieramy wprowadzony model

    if (model === "") {
        alert("Proszę wpisać nazwę modelu.");
        return;
    }

    // Wysłanie zapytania do PHP za pomocą fetch
    fetch(`search_car.php?model=${model}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Wyświetlenie wyników wyszukiwania
                document.getElementById("search-result").innerHTML = 
                    `<p>Znaleziono model: ${data.car.model}</p>`;
            } else {
                document.getElementById("search-result").innerHTML = 
                    `<p>Nie znaleziono samochodu o tej nazwie.</p>`;
            }
        })
        .catch(error => {
            console.error('Błąd:', error);
            document.getElementById("search-result").innerHTML = 
                `<p>Wystąpił błąd podczas wyszukiwania.</p>`;
        });
}
