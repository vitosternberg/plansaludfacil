const Validaciones = {
    email: (correo) => {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(correo);
    },
    rut: (rut) => {
        // Formato basico y DV
        if (!/^[0-9]+-[0-9kK]{1}$/.test(rut)) return false;
        let tmp = rut.split('-');
        let digv = tmp[1].toLowerCase(); 
        let rutP = tmp[0];
        let M=0,S=1;
        for(;rutP;rutP=Math.floor(rutP/10))
            S=(S+rutP%10*(9-M++%6))%11;
        let dv = S?S-1:'k';
        return (dv == digv);
    },
    telefono: (tel) => {
        // Valida que sean 9 digitos
        return /^[0-9]{9}$/.test(tel.replace(/\s/g, ''));
    },
    texto: (txt) => {
        return txt.trim().length >= 2;
    },
    numero: (num) => {
        return num !== '' && !isNaN(num) && Number(num) > 0;
    }
};

document.addEventListener('DOMContentLoaded', () => {
    // Escuchar eventos 'input' y 'blur' en todos los inputs para validacion en tiempo real
    const inputs = document.querySelectorAll('input[required], select[required]');
    
    inputs.forEach(input => {
        input.addEventListener('input', function() {
            if (this.name === 'url_website') return; // Honeypot
            
            let isValid = true;
            let val = this.value;

            if (this.name === 'email') isValid = Validaciones.email(val);
            else if (this.name === 'rut') isValid = Validaciones.rut(val);
            else if (this.name === 'phone') isValid = Validaciones.telefono(val);
            else if (this.name === 'name' || this.name === 'comuna') isValid = Validaciones.texto(val);
            else if (this.name === 'age' || this.name === 'income' || this.name === 'cargas') isValid = Validaciones.numero(val);

            // Tailwind CSS styling para feedback visual
            if (val === '') {
                this.classList.remove('border-red-500', 'border-green-500');
            } else if (isValid) {
                this.classList.remove('border-red-500');
                this.classList.add('border-green-500', 'border-2');
            } else {
                this.classList.remove('border-green-500');
                this.classList.add('border-red-500', 'border-2');
            }
        });
    });
});

// Funcion global para ser llamada en el 'submit' del formulario
window.validarFormularioCompleto = function(formElement) {
    let inputs = formElement.querySelectorAll('input[required], select[required]');
    let todosValidos = true;
    let mensajeError = "Por favor, completa correctamente los campos destacados en rojo.";

    inputs.forEach(input => {
        let val = input.value;
        let isValid = true;

        if (input.name === 'email') isValid = Validaciones.email(val);
        else if (input.name === 'rut') isValid = Validaciones.rut(val);
        else if (input.name === 'phone') isValid = Validaciones.telefono(val);
        else if (input.name === 'name' || input.name === 'comuna') isValid = Validaciones.texto(val);
        else if (input.name === 'age' || input.name === 'income' || input.name === 'cargas') isValid = Validaciones.numero(val);

        if (!isValid || val.trim() === '') {
            todosValidos = false;
            input.classList.remove('border-green-500');
            input.classList.add('border-red-500', 'border-2');
        } else {
            input.classList.remove('border-red-500');
            input.classList.add('border-green-500', 'border-2');
        }
    });

    return { valido: todosValidos, mensaje: mensajeError };
};
