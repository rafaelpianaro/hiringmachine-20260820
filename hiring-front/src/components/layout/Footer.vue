<template>
  <footer class="bg-graphite text-mint">
    <div class="max-w-7xl mx-auto px-6 py-16">
      <div class="grid md:grid-cols-4 gap-10">
        <div class="md:col-span-1">
          <span class="font-serif text-2xl font-semibold text-white">Recepies</span>
          <p class="mt-4 text-sm text-sage leading-relaxed">
            Descubra, cozinhe e compartilhe pratos incríveis. Uma comunidade feita por quem ama cozinhar.
          </p>
        </div>
        <div>
          <h4 class="text-white font-medium mb-4 text-sm uppercase tracking-wide">Navegação</h4>
          <ul class="space-y-2 text-sm text-sage">
            <li><RouterLink to="/" class="hover:text-olive transition">Início</RouterLink></li>
            <li><a href="/#receitas" class="hover:text-olive transition">Receitas</a></li>
            <li><RouterLink to="/minhas-receitas" class="hover:text-olive transition">Minhas Receitas</RouterLink></li>
            <li><a href="#" class="hover:text-olive transition">Sobre</a></li>
          </ul>
        </div>
        <div>
          <h4 class="text-white font-medium mb-4 text-sm uppercase tracking-wide">Categorias</h4>
          <ul class="space-y-2 text-sm text-sage">
            <li><a href="#" class="hover:text-olive transition">Sobremesas</a></li>
            <li><a href="#" class="hover:text-olive transition">Sopas</a></li>
            <li><a href="#" class="hover:text-olive transition">Vegetariano</a></li>
            <li><a href="#" class="hover:text-olive transition">Carnes</a></li>
          </ul>
        </div>
        <div>
          <h4 class="text-white font-medium mb-4 text-sm uppercase tracking-wide">Redes Sociais</h4>
          <div class="flex gap-3">
            <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-olive transition">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
            </a>
            <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-olive transition">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
            </a>
            <a href="#" class="w-10 h-10 rounded-full bg-white/10 flex items-center justify-center hover:bg-olive transition">
              <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"/></svg>
            </a>
          </div>
        </div>
      </div>
      <div class="mt-12 pt-8 border-t border-white/10 flex flex-col md:flex-row justify-between items-center gap-4 text-xs text-sage">
        <p>© 2026 Recepies. Todos os direitos reservados.</p>
        <div class="flex gap-6">
          <a href="#" class="hover:text-white transition">Política de Privacidade</a>
          <a href="#" class="hover:text-white transition">Termos de Uso</a>
        </div>
      </div>
    </div>
  </footer>
</template>
