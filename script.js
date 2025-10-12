// Toggle do menu para dispositivos móveis
    const menuToggle = document.querySelector('.menu-toggle');
    const nav = document.querySelector('header nav');

    menuToggle.addEventListener('click', () => {
        nav.classList.toggle('active');
        menuToggle.textContent = nav.classList.contains('active') ? '✕' : '☰';
    });

    // Smooth scroll para links de navegação
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            window.scrollTo({
                top: target.offsetTop - 80,
                behavior: 'smooth'
            });

            if (nav.classList.contains('active')) {
                nav.classList.remove('active');
                menuToggle.textContent = '☰';
            }
        });
    });

    // Toggle de informações extras nos cards de serviço
    document.querySelectorAll('.servico-card').forEach(card => {
        card.addEventListener('click', () => {
            card.classList.toggle('active');
        });
    });

    // Envio do formulário para o backend PHP
    const form = document.querySelector('.contact-form');
    const formMessage = document.querySelector('.form-message');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const nome = document.getElementById('nome').value.trim();
        const email = document.getElementById('email').value.trim();
        const mensagem = document.getElementById('mensagem').value.trim();

        if (nome && email && mensagem) {
            try {
                const response = await fetch('submit_form.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ nome, email, mensagem })
                });
                const data = await response.json();
                formMessage.textContent = data.message;
                formMessage.className = `form-message ${data.status}`;
                if (data.status === 'success') {
                    form.reset();
                }
            } catch (error) {
                formMessage.textContent = 'Erro ao enviar a mensagem. Tente novamente.';
                formMessage.className = 'form-message error';
            }
        } else {
            formMessage.textContent = 'Por favor, preencha todos os campos corretamente.';
            formMessage.className = 'form-message error';
        }

        setTimeout(() => {
            formMessage.textContent = '';
            formMessage.className = 'form-message';
        }, 5000);
    });