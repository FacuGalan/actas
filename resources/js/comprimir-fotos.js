/**
 * Comprime las fotos en el navegador antes de que Livewire las suba.
 *
 * Las fotos de celular pesan varios MB y el servidor tiene que descomprimirlas enteras
 * en memoria para procesarlas. Achicándolas acá llegan livianas (unos cientos de KB),
 * se ahorra ancho de banda del inspector y RAM/CPU del servidor. El procesamiento del
 * servidor (comprimirFotoSiEsNecesario) sigue como red de seguridad.
 *
 * Uso: agregar el atributo data-comprimir-foto al <input type="file" wire:model="...">.
 */

const LADO_MAX = 1920;
const CALIDAD_JPEG = 0.8;

async function cargarImagen(archivo) {
    const url = URL.createObjectURL(archivo);
    try {
        const img = new Image();
        img.src = url;
        // El navegador aplica la orientación EXIF al decodificar, así la foto no queda acostada.
        await img.decode();
        return img;
    } finally {
        URL.revokeObjectURL(url);
    }
}

async function comprimir(archivo) {
    const img = await cargarImagen(archivo);

    const escala = Math.min(1, LADO_MAX / Math.max(img.naturalWidth, img.naturalHeight));
    const ancho = Math.round(img.naturalWidth * escala);
    const alto = Math.round(img.naturalHeight * escala);

    const canvas = document.createElement('canvas');
    canvas.width = ancho;
    canvas.height = alto;
    canvas.getContext('2d').drawImage(img, 0, 0, ancho, alto);

    const blob = await new Promise((resolve) => canvas.toBlob(resolve, 'image/jpeg', CALIDAD_JPEG));
    if (!blob) {
        throw new Error('No se pudo generar la imagen comprimida');
    }

    const nombre = archivo.name.replace(/\.[^.]+$/, '') + '.jpg';
    return new File([blob], nombre, { type: 'image/jpeg', lastModified: Date.now() });
}

function mostrarAviso(input, visible) {
    const aviso = input.closest('.relative')?.querySelector('[data-aviso-compresion]');
    aviso?.classList.toggle('hidden', !visible);
}

// Se escucha en fase de captura para interceptar el cambio antes que el listener de wire:model.
document.addEventListener('change', async (e) => {
    const input = e.target;
    if (!(input instanceof HTMLInputElement) || !input.hasAttribute('data-comprimir-foto')) {
        return;
    }

    // Este es el evento que disparamos nosotros con la foto ya comprimida: dejarlo pasar a Livewire.
    if (input.dataset.fotoComprimida === '1') {
        delete input.dataset.fotoComprimida;
        return;
    }

    const archivo = input.files?.[0];
    if (!archivo) {
        return;
    }

    e.stopImmediatePropagation();
    mostrarAviso(input, true);

    let archivoFinal = archivo;
    try {
        archivoFinal = await comprimir(archivo);
    } catch (error) {
        // Si el navegador no puede procesarla, se sube la original y el servidor la valida/comprime.
        console.warn('No se pudo comprimir la foto en el navegador:', error);
    } finally {
        mostrarAviso(input, false);
    }

    const transferencia = new DataTransfer();
    transferencia.items.add(archivoFinal);
    input.files = transferencia.files;

    input.dataset.fotoComprimida = '1';
    input.dispatchEvent(new Event('change', { bubbles: true }));
}, true);
