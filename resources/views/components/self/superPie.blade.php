<footer x-data="{ openModal: '' }" @keydown.escape.window="openModal = ''" class="bg-black text-gray-400">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-10">
            <div>
                <h3 class="text-lg font-bold text-white mb-4">Foto Valera</h3>
                <p class="text-sm">
                    Capturando la esencia del paisaje y la vida salvaje. Explora el mundo a través de mi lente.
                </p>
                <div class="flex items-center space-x-4 mt-6">
                    <a href="https://www.instagram.com/foto_valera/" target="_blank" rel="noopener noreferrer"
                        class="hover:text-white transition-colors duration-300" aria-label="Instagram">
                        <i class="fab fa-instagram fa-lg">Instagram</i>
                    </a>
                    <a href="https://www.facebook.com/FotoValera" target="_blank" rel="noopener noreferrer"
                        class="hover:text-white transition-colors duration-300" aria-label="Facebook">
                        <i class="fab fa-facebook-f fa-lg">Facebook</i>
                    </a>
                </div>
            </div>


            <div>
                <h3 class="text-sm font-semibold tracking-wider text-white uppercase mb-4">Categorías de nuestro Blog</h3>
                <ul class="space-y-2 text-sm">
                    <li><a href="#" class="hover:text-white transition-colors duration-300">Mejores
                            Fotografías</a></li>
                    <li><a href="#" class="hover:text-white transition-colors duration-300">Consejos de
                            Composición</a></li>
                    <li><a href="#" class="hover:text-white transition-colors duration-300">Fotografía de
                            Paisajes</a></li>
                    <li><a href="#" class="hover:text-white transition-colors duration-300">Procesamiento y
                            Edición</a></li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold tracking-wider text-white uppercase mb-4">Contacto</h3>
                <ul class="space-y-3 text-sm">
                    <li class="flex items-start">
                        <i class="fas fa-phone mt-1 mr-3 text-white"></i>
                        <a href="tel:+34660581178" class="hover:text-white transition-colors duration-300">+34 660 581
                            178</a>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-envelope mt-1 mr-3 text-white"></i>
                        <a href="mailto:infofotovalera@gmail.com"
                            class="hover:text-white transition-colors duration-300">infofotovalera@gmail.com</a>
                    </li>
                </ul>
            </div>

            <div>
                <h3 class="text-sm font-semibold tracking-wider text-white uppercase mb-4">Información</h3>
                <ul class="space-y-2 text-sm">
                    <li><button @click="openModal = 'privacy'"
                            class="text-left hover:text-white transition-colors duration-300">Política de
                            Privacidad</button></li>
                    <li><button @click="openModal = 'legal'"
                            class="text-left hover:text-white transition-colors duration-300">Aviso Legal</button></li>
                    <li><button @click="openModal = 'cookies'"
                            class="text-left hover:text-white transition-colors duration-300">Política de
                            Cookies</button></li>
                </ul>
            </div>
        </div>
        @php
            use Illuminate\Support\Facades\Storage;

            $disk = Storage::disk('logos');
            $expires = now()->addMinutes(60); // tiempo de validez de la URL

            $fuji = $disk->temporaryUrl('fuji-trucolor.svg', $expires);
            $canon = $disk->temporaryUrl('canon-logo.svg', $expires);
            $nikon = $disk->temporaryUrl('nikon-2.svg', $expires);
            $epson = $disk->temporaryUrl('epson-2.svg', $expires);

        @endphp
        <div class="border-t border-gray-800 pt-8">
            <h3 class="text-center text-sm font-semibold tracking-wider text-white uppercase mb-6">Colaboradores y
                Reconocimientos</h3>
            <div class="flex flex-wrap justify-center items-center gap-8">
                <a href="https://www.epson.es/" target="_blank" rel="noopener noreferrer">
                    <img src="{{ $epson }}" alt="Epson"
                        class="h-8 grayscale hover:grayscale-0 transition-all duration-300">
                </a>
                <a href="https://www.nikon.es/" target="_blank" rel="noopener noreferrer">
                    <img src="{{ $nikon }}" alt="Nikon"
                        class="h-7 grayscale hover:grayscale-0 transition-all duration-300">
                </a>
                <a href="https://www.fujifilm.com/es/es-es" target="_blank" rel="noopener noreferrer">
                    <img src="{{ $fuji }}" alt="Fuji"
                        class="h-8 grayscale hover:grayscale-0 transition-all duration-300">
                </a>
                <a href="https://www.canon.es/" target="_blank" rel="noopener noreferrer">
                    <img src="{{ $canon }}" alt="Canon"
                        class="h-7 grayscale hover:grayscale-0 transition-all duration-300">
                </a>
            </div>
        </div>

    </div>
    <div class="bg-gray-950/50 py-4 px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto text-center text-xs">
            <p>&copy; 2025 Foto Valera. Todos los derechos reservados.</p>
        </div>
    </div>


    <div x-show="openModal === 'privacy'" x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4" style="display: none;">
        <div @click.outside="openModal = ''"
            class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-xl font-semibold text-gray-800">Política de Privacidad</h3>
                <button @click="openModal = ''" class="text-gray-500 hover:text-gray-800">&times;</button>
            </div>
            <div class="p-6 overflow-y-auto">
                <article class="prose lg:prose-xl max-w-none text-gray-700">
                    <p class="text-sm text-gray-500">Última actualización: 18 de junio de 2025</p>
                    <h2>1. Responsable del Tratamiento de Datos</h2>
                    <ul>
                        <li><strong>Titular:</strong> Ivan Senkin Gorbatova</li>
                        <li><strong>Domicilio:</strong>Almeria, España</li>
                        <li><strong>Correo electrónico:</strong> infofotovalera@gmail.com</li>
                    </ul>
                    <h2>2. Finalidad del Tratamiento de Datos</h2>
                    <p>Recogemos y tratamos la información que nos facilitas para gestionar consultas, tramitar la
                        contratación de servicios y, con tu consentimiento, enviar comunicaciones comerciales.</p>
                    <h2>3. Legitimación para el Tratamiento</h2>
                    <p>La base legal es tu <strong>consentimiento</strong> y, en caso de contratación, la
                        <strong>ejecución de un contrato</strong>.
                    </p>
                    <h2>4. Tus Derechos de Protección de Datos</h2>
                    <p>Puedes ejercer tus derechos de acceso, rectificación, supresión, oposición, limitación y
                        portabilidad enviando un correo a <strong>infofotovalera@gmail.com</strong> adjuntando copia de
                        tu DNI.</p>
                </article>
            </div>
        </div>
    </div>

    <div x-show="openModal === 'legal'" x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4" style="display: none;">
        <div @click.outside="openModal = ''"
            class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-xl font-semibold text-gray-800">Aviso Legal</h3>
                <button @click="openModal = ''" class="text-gray-500 hover:text-gray-800">&times;</button>
            </div>
            <div class="p-6 overflow-y-auto">
                <article class="prose lg:prose-xl max-w-none text-gray-700">
                    <p class="text-sm text-gray-500">Última actualización: 18 de junio de 2025</p>
                    <h2>1. Datos del Titular de la Web</h2>
                    <ul>
                        <li><strong>Titular:</strong> Ivan Senkin Gorbatova</li>
                        <li><strong>Correo:</strong> infofotovalera@gmail.com</li>
                    </ul>
                    <h2>2. Propiedad Intelectual e Industrial</h2>
                    <p>Todo el contenido del Sitio Web, incluyendo textos y fotografías, es propiedad de <strong>Ivan
                            Senkin Gorbatova</strong>. Queda estrictamente prohibida su reproducción o distribución sin
                        consentimiento explícito y por escrito.</p>
                    <h2>3. Ley Aplicable y Jurisdicción</h2>
                    <p>Para la resolución de cualquier controversia, será de aplicación la legislación española,
                        sometiéndose a los Juzgados y Tribunales de [Ciudad de tus Juzgados, ej: Almería].</p>
                </article>
            </div>
        </div>
    </div>

    <div x-show="openModal === 'cookies'" x-transition
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-75 p-4" style="display: none;">
        <div @click.outside="openModal = ''"
            class="bg-white rounded-lg shadow-xl w-full max-w-4xl max-h-[90vh] flex flex-col">
            <div class="flex justify-between items-center p-4 border-b">
                <h3 class="text-xl font-semibold text-gray-800">Política de Cookies</h3>
                <button @click="openModal = ''" class="text-gray-500 hover:text-gray-800">&times;</button>
            </div>
            <div class="p-6 overflow-y-auto">
                <article class="prose lg:prose-xl max-w-none text-gray-700">
                    <p class="text-sm text-gray-500">Última actualización: 18 de junio de 2025</p>
                    <h2>1. ¿Qué son las cookies?</h2>
                    <p>Una cookie es un pequeño fichero que se almacena en tu navegador para recordar tu visita. Este
                        sitio web utiliza cookies técnicas y de análisis para mejorar tu experiencia.</p>
                    <h2>2. Cookies utilizadas</h2>
                    <p><strong>IMPORTANTE:</strong> Debes auditar tu web para listar las cookies exactas.</p>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 mt-4">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">Cookie
                                    </th>
                                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                        Proveedor</th>
                                    <th class="px-2 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                        Finalidad</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr>
                                    <td class="px-2 py-2">_ga</td>
                                    <td class="px-2 py-2">Google Analytics</td>
                                    <td class="px-2 py-2">Distinguir usuarios.</td>
                                </tr>
                                <tr>
                                    <td class="px-2 py-2">PHPSESSID</td>
                                    <td class="px-2 py-2">Propia</td>
                                    <td class="px-2 py-2">Sesión de usuario.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <h2>3. ¿Cómo desactivar las cookies?</h2>
                    <p>Puedes configurar tu navegador para bloquear o eliminar las cookies. Consulta la ayuda de tu
                        navegador para más información.</p>
                </article>
            </div>
        </div>
    </div>

</footer>
