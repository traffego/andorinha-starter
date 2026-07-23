        <section data-bs-version="5.1" class="footer3 cid-upLeeCZin5 mbr-reveal"
            once="footers" id="footer3-h"
            style="z-index: -999; position: fixed; bottom: 0px; width: 100%;">
            <div class="container">
                <div class="media-container-row align-center mbr-white">
                    <div class="row row-copirayt">
                        <p class="mbr-text mb-0 mbr-fonts-style mbr-white align-center display-7 animate__animated animate__delay-1s animate__fadeInUp">
                            © Copyright <?php echo date('Y'); ?> <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:white">Andorinha Negócios Criativos. Todos Direitos Reservados.</a><br>
                            <em><a href="mailto:lcaraujo4252@gmail.com" style="font-size: small; color:white">developer by Lucas Calado</a></em>
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <div id="scrollToTop" class="scrollToTop mbr-arrow-up">
            <a style="text-align: center;"><i class="mbr-arrow-up-icon mbr-arrow-up-icon-cm cm-icon cm-icon-smallarrow-up"></i></a>
        </div>
        <input name="animation" type="hidden">

        <script type="text/javascript">
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('form');
                const btn = document.getElementById('button');

                if (form && btn) {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault();
                        btn.value = 'Enviando...';

                        const serviceID = 'default_service';
                        const templateID = 't_default';

                        if (typeof emailjs !== 'undefined') {
                            emailjs.sendForm(serviceID, templateID, this)
                                .then(() => {
                                    btn.value = 'Enviar';
                                    alert('Mensagem enviada com sucesso!');
                                }, (err) => {
                                    btn.value = 'Enviar';
                                    alert('Erro ao enviar mensagem: ' + JSON.stringify(err));
                                });
                        } else {
                            btn.value = 'Enviar';
                            alert('Serviço de e-mail indisponível.');
                        }
                    });
                }
            });
        </script>

        <?php wp_footer(); ?>
    </body>
</html>
