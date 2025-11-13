{{-- MUDANÇA 1: Reduzido o espaço em branco (padding) de 'pt-5 pb-4' para 'pt-4 pb-4' --}}
<footer class="footer mt-5 pt-4 pb-4 bg-gray-100">
  <div class="container-fluid">
    
    {{-- Adicionada uma linha horizontal no topo para separar do conteúdo --}}
    <hr class="horizontal dark mt-0 mb-4">
    
    <div class="row align-items-center">
      
      {{-- Coluna 1: Contactos (Ocupa 6 de 12 colunas) --}}
      <div class="col-lg-6 col-md-6 mb-4 mb-md-0">
        <h6 class="font-weight-bolder">Contactos</h6>
        {{-- MUDANÇA 2: Texto de contacto mais compacto (menos parágrafos) --}}
        <p class="text-sm text-secondary mb-1">
          <strong>Comunidade Intermunicipal da Beira Baixa</strong>
        </p>
        <p class="text-xs text-secondary mb-1">
          Praça Rainha D. Leonor, Ed. Emblemas, 2º andar | 6000-117 Castelo Branco
        </p>
        <p class="text-xs text-secondary mb-0">
          +351 272 342 540 (rede fixa) | geral@cimbb.pt
        </p>
      </div>

      {{-- Coluna 2: Redes Sociais (Ocupa 6 de 12 colunas) --}}
      <div class="col-lg-6 col-md-6">
        <h6 class="font-weight-bolder">Redes Sociais</h6>
        
        {{-- MUDANÇA 3: Lista <ul> removida. Links agora em linha (flex-row) para poupar espaço --}}
        <div class="d-flex flex-row flex-wrap">
          <a href="https://www.facebook.com/CIMBeiraBaixa" class="nav-link text-secondary p-0 me-3 mb-2" target="_blank">
            <i class="fab fa-facebook me-1"></i> CIMBeiraBaixa
          </a>
          <a href="https://www.facebook.com/BeiraBaixaPT" class="nav-link text-secondary p-0 me-3 mb-2" target="_blank">
            <i class="fab fa-facebook me-1"></i> BeiraBaixaPT
          </a>
          <a href="https://www.instagram.com/beirabaixapt" class="nav-link text-secondary p-0 me-3 mb-2" target="_blank">
            <i class="fab fa-instagram me-1"></i> @beirabaixapt
          </a>
          <a href="https://pt.linkedin.com/company/cimbeirabaixa" class="nav-link text-secondary p-0 me-3 mb-2" target="_blank">
            <i class="fab fa-linkedin me-1"></i> cimbeirabaixa
          </a>
          <a href="httpsa://www.youtube.com/@beirabaixa" class="nav-link text-secondary p-0 me-3 mb-2" target="_blank">
            <i class="fab fa-youtube me-1"></i> @beirabaixa
          </a>
        </div>
      </div>
      
    </div>
  </div>
</footer>