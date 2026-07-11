const DEFAULT_NUMBER = '5511983657862';

const form = document.getElementById('orcamento-form');
const imageInput = document.getElementById('foto');
const imagePreview = document.getElementById('foto-preview');
const statusMessage = document.getElementById('status-message');
const cameraButton = document.getElementById('foto-camera-btn');
const galleryButton = document.getElementById('foto-galeria-btn');

const openImagePicker = (mode) => {
  if (!imageInput) {
    return;
  }

  imageInput.value = '';

  if (mode === 'camera') {
    imageInput.setAttribute('capture', 'environment');
  } else {
    imageInput.removeAttribute('capture');
  }

  imageInput.click();
};

if (cameraButton && imageInput) {
  cameraButton.addEventListener('click', () => openImagePicker('camera'));
}

if (galleryButton && imageInput) {
  galleryButton.addEventListener('click', () => openImagePicker('gallery'));
}

if (imageInput && imagePreview) {
  imageInput.addEventListener('change', () => {
    const file = imageInput.files && imageInput.files[0];
    if (!file) {
      imagePreview.innerHTML = '';
      return;
    }

    const reader = new FileReader();
    reader.onload = (event) => {
      imagePreview.innerHTML = `<img src="${event.target.result}" alt="Imagem do local">`;
    };
    reader.readAsDataURL(file);
  });
}

if (form) {
  form.addEventListener('submit', async (event) => {
    event.preventDefault();

    const nome = document.getElementById('nome').value.trim();
    const whatsapp = document.getElementById('whatsapp').value.trim();
    const metros = document.getElementById('metros').value.trim();
    const endereco = document.getElementById('endereco').value.trim();
    const servicoSelect = document.getElementById('servico');
    const servicoInput = document.querySelector('input[name="servico"]:checked');
    const servico = servicoSelect ? servicoSelect.value : (servicoInput ? servicoInput.value : 'manutencao');
    const observacoes = document.getElementById('observacoes').value.trim();
    const file = imageInput && imageInput.files ? imageInput.files[0] : null;

    const servicos = {
      manutencao: 'Manutenção',
      jardim: 'Jardim novo',
      premium: 'Paisagismo premium'
    };

    const valores = {
      manutencao: 15,
      jardim: 35,
      premium: 70
    };

    const valorEstimado = Number(metros || 0) * (valores[servico] || 15);

    const formData = new FormData();
    formData.append('nome', nome);
    formData.append('whatsapp', whatsapp);
    formData.append('metros', metros);
    formData.append('endereco', endereco);
    formData.append('servico', servico);
    formData.append('observacoes', observacoes);

    if (file) {
      formData.append('foto', file);
    }

    if (statusMessage) {
      statusMessage.textContent = 'Enviando imagem e abrindo o WhatsApp...';
    }

    try {
      let mensagem = `Olá! Novo orçamento recebido:\n`;
      mensagem += `Nome: ${nome || 'Não informado'}\n`;
      mensagem += `WhatsApp: ${whatsapp || 'Não informado'}\n`;
      mensagem += `Área: ${metros || 'Não informada'} m²\n`;
      mensagem += `Endereço: ${endereco || 'Não informado'}\n`;
      mensagem += `Serviço: ${servicos[servico] || servico}\n`;
      mensagem += `Valor estimado: R$ ${valorEstimado.toFixed(2).replace('.', ',')}\n`;
      mensagem += `Observação: valor sujeito a alteração conforme avaliação do local.\n`;
      mensagem += `Detalhes: ${observacoes || 'Nenhum detalhe informado'}\n`;

      if (file) {
        mensagem += `\nFoto do local anexada no compartilhamento.`;
      } else {
        mensagem += `\nImagem anexada: Nenhuma`;
      }

      if (file) {
        const shareData = {
          files: [file],
          title: 'Novo orçamento',
          text: mensagem
        };

        if (navigator.share && navigator.canShare && navigator.canShare(shareData)) {
          if (statusMessage) {
            statusMessage.textContent = 'Abrindo o compartilhamento do celular com a foto...';
          }

          try {
            await navigator.share(shareData);
            if (statusMessage) {
              statusMessage.textContent = 'Compartilhamento concluído. Se necessário, finalize o envio no WhatsApp.';
            }
            return;
          } catch (shareError) {
            console.warn('Falha no compartilhamento nativo:', shareError);
          }
        }
      }

      const url = `https://wa.me/${DEFAULT_NUMBER}?text=${encodeURIComponent(mensagem)}`;
      if (statusMessage) {
        statusMessage.textContent = 'O navegador não conseguiu anexar a foto automaticamente. O WhatsApp será aberto para você anexar a imagem manualmente.';
      }
      window.open(url, '_blank', 'noopener,noreferrer');
    } catch (error) {
      if (statusMessage) {
        statusMessage.textContent = error.message || 'Não foi possível abrir o WhatsApp.';
      }
      console.error(error);
    }
  });
}
