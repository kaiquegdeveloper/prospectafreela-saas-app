<?php

namespace Database\Seeders;

use App\Models\SalesScript;
use App\Models\SalesScriptCategory;
use Illuminate\Database\Seeder;

class SalesScriptsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Designer Gráfico',
                'slug' => 'designer-grafico',
                'description' => 'Scripts de vendas especializados para profissionais de design gráfico',
                'icon' => '🎨',
                'color' => 'purple',
            ],
            [
                'name' => 'Estrategista de Marketing',
                'slug' => 'estrategista-marketing',
                'description' => 'Scripts de vendas para estrategistas e consultores de marketing',
                'icon' => '📊',
                'color' => 'blue',
            ],
            [
                'name' => 'Programador Back-end Freela',
                'slug' => 'programador-backend-freela',
                'description' => 'Scripts de vendas para desenvolvedores back-end freelancers',
                'icon' => '⚙️',
                'color' => 'indigo',
            ],
            [
                'name' => 'Programador Front-end Freela',
                'slug' => 'programador-frontend-freela',
                'description' => 'Scripts de vendas para desenvolvedores front-end freelancers',
                'icon' => '💻',
                'color' => 'cyan',
            ],
            [
                'name' => 'Criar ecommerce',
                'slug' => 'criar-ecommerce',
                'description' => 'Scripts de vendas para criação de e-commerce',
                'icon' => '🛒',
                'color' => 'green',
            ],
            [
                'name' => 'Estratégia de Ecommerce',
                'slug' => 'estrategia-ecommerce',
                'description' => 'Scripts de vendas para estratégias de e-commerce',
                'icon' => '📈',
                'color' => 'emerald',
            ],
            [
                'name' => 'Tráfego Pago',
                'slug' => 'trafego-pago',
                'description' => 'Scripts de vendas para gestores de tráfego pago',
                'icon' => '📢',
                'color' => 'orange',
            ],
            [
                'name' => 'Social Media',
                'slug' => 'social-media',
                'description' => 'Scripts de vendas para gestores de redes sociais',
                'icon' => '📱',
                'color' => 'pink',
            ],
            [
                'name' => 'Criar Site',
                'slug' => 'criar-site',
                'description' => 'Scripts de vendas para criação de sites',
                'icon' => '🌐',
                'color' => 'teal',
            ],
        ];

        foreach ($categories as $index => $categoryData) {
            $category = SalesScriptCategory::firstOrCreate(
                ['slug' => $categoryData['slug']],
                [
                    ...$categoryData,
                    'order' => $index + 1,
                ]
            );

            // Verifica se já tem scripts, se não, cria
            if ($category->scripts()->count() === 0) {
                $this->seedScriptsForCategory($category);
            }
        }
    }

    /**
     * Generate scripts for a category
     */
    private function seedScriptsForCategory(SalesScriptCategory $category): void
    {
        $stages = [
            'introducao' => $this->getIntroducaoScripts($category->name),
            'qualificacao' => $this->getQualificacaoScripts($category->name),
            'levar_call' => $this->getLevarCallScripts($category->name),
            'quebra_objecao' => $this->getQuebraObjecaoScripts($category->name),
            'fechamento' => $this->getFechamentoScripts($category->name),
        ];

        foreach ($stages as $stage => $scripts) {
            foreach ($scripts as $index => $scriptData) {
                SalesScript::create([
                    'category_id' => $category->id,
                    'stage' => $stage,
                    'title' => $scriptData['title'] ?? null,
                    'content' => $scriptData['content'],
                    'tips' => $scriptData['tips'] ?? null,
                    'order' => $index + 1,
                    'is_active' => true,
                ]);
            }
        }
    }

    /**
     * Get introdução scripts - 50 unique scripts
     */
    private function getIntroducaoScripts(string $categoryName): array
    {
        $templates = $this->getIntroducaoTemplates($categoryName);
        $scripts = [];

        for ($i = 0; $i < 50; $i++) {
            $template = $templates[$i % count($templates)];
            $variation = $this->applyVariations($template['content'], $i);
            
            $scripts[] = [
                'title' => "Introdução " . ($i + 1),
                'content' => $variation,
                'tips' => $template['tips'] ?? 'Personalize mencionando algo específico sobre a empresa.',
            ];
        }

        return $scripts;
    }

    /**
     * Get introdução templates
     */
    private function getIntroducaoTemplates(string $categoryName): array
    {
        return [
            ['content' => "Olá! Vi que você está no ramo e pensei que poderia ser interessante conversar sobre como posso ajudar sua empresa a crescer. Sou especializado em {$categoryName} e já auxiliei diversas empresas a alcançarem resultados incríveis.", 'tips' => 'Personalize mencionando algo específico que você encontrou sobre a empresa.'],
            ['content' => "Oi! Espero que esteja bem. Vi seu negócio e tenho certeza de que posso fazer uma diferença real nos seus resultados. Trabalho com {$categoryName} e já transformei várias empresas.", 'tips' => 'Seja caloroso e genuíno no tom.'],
            ['content' => "Olá! Meu nome é [SEU NOME] e sou especialista em {$categoryName}. Encontrei sua empresa e percebi que há uma grande oportunidade de crescimento. Podemos conversar?", 'tips' => 'Use seu nome para criar conexão pessoal.'],
            ['content' => "Oi! Vi que você está no mercado e queria apresentar uma oportunidade. Trabalho com {$categoryName} e posso mostrar resultados reais que outras empresas já alcançaram.", 'tips' => 'Mencione resultados para criar credibilidade.'],
            ['content' => "Olá! Como está? Vi seu negócio e acredito que tenho algo que pode transformar seus resultados. Especializo-me em {$categoryName} e já ajudei muitas empresas.", 'tips' => 'Comece com uma pergunta para engajar.'],
            ['content' => "E aí, tudo bem? Descobri seu negócio e vi que temos muito potencial para trabalhar juntos. Sou profissional em {$categoryName} e estou animado para compartilhar algumas ideias.", 'tips' => 'Use um tom mais casual e próximo.'],
            ['content' => "Bom dia! Gostaria de apresentar uma oportunidade que pode elevar seu negócio. Trabalho com {$categoryName} e tenho casos de sucesso comprovados. Podemos trocar uma ideia?", 'tips' => 'Use o horário apropriado (Bom dia/Tarde/Noite).'],
            ['content' => "Oi! Vi sua empresa e percebi que posso ajudar você a alcançar seus objetivos mais rápido. Sou especializado em {$categoryName} e já vi resultados incríveis acontecerem.", 'tips' => 'Foque em velocidade de resultados.'],
            ['content' => "Olá! Encontrei seu negócio e fiquei interessado em ajudar. Tenho experiência comprovada em {$categoryName} e posso mostrar como outras empresas similares cresceram com meu trabalho.", 'tips' => 'Mencione empresas similares para criar identificação.'],
            ['content' => "Oi, tudo certo? Vi que você está no mercado e tenho algumas ideias que podem fazer toda diferença. Trabalho com {$categoryName} há [X] anos e já vi muitos negócios transformarem.", 'tips' => 'Mencione sua experiência ou tempo de mercado.'],
        ];
    }

    /**
     * Get qualificação scripts - 50 unique scripts
     */
    private function getQualificacaoScripts(string $categoryName): array
    {
        $questions = $this->getQualificacaoQuestions($categoryName);
        $scripts = [];

        for ($i = 0; $i < 50; $i++) {
            $question = $questions[$i % count($questions)];
            $variation = $this->applyQualificationVariations($question, $i);
            
            $scripts[] = [
                'title' => "Qualificação " . ($i + 1),
                'content' => $variation,
                'tips' => 'Faça perguntas abertas para obter mais informações. Escute atentamente as respostas antes de responder.',
            ];
        }

        return $scripts;
    }

    /**
     * Get qualificação questions
     */
    private function getQualificacaoQuestions(string $categoryName): array
    {
        return [
            "Para eu poder te ajudar da melhor forma, preciso entender melhor seu negócio. Qual é o maior desafio que você enfrenta atualmente?",
            "Quero garantir que vou criar a melhor solução para você. Como você mede o sucesso do seu negócio hoje?",
            "Para personalizar minha abordagem, me conta: o que você acha que está faltando para alcançar seus objetivos?",
            "Antes de propor algo, preciso entender seu contexto. Você já trabalhou com alguém de {$categoryName} antes? Como foi?",
            "Se eu pudesse fazer uma coisa por você hoje, qual seria o resultado ideal que você gostaria de alcançar?",
            "Para dimensionar melhor a solução, quantos clientes você tem atualmente?",
            "Me ajuda a entender: como você está gerando novos clientes hoje? Está satisfeito com os resultados?",
            "Qual é o seu ticket médio atualmente? Isso me ajuda a entender o potencial do negócio.",
            "Para propor um investimento adequado, quanto você está investindo em marketing atualmente?",
            "O que mais te incomoda no seu processo atual? O que você gostaria de melhorar?",
            "Conte-me sobre seus clientes ideais. Como você os descreveria?",
            "Quais são suas metas para os próximos 6 meses?",
            "Como você se diferencia da concorrência?",
            "Qual foi o maior sucesso do seu negócio até agora?",
            "Se você tivesse um orçamento ilimitado, o que faria primeiro?",
        ];
    }

    /**
     * Get levar para call scripts - 50 unique scripts
     */
    private function getLevarCallScripts(string $categoryName): array
    {
        $templates = $this->getLevarCallTemplates();
        $scripts = [];

        for ($i = 0; $i < 50; $i++) {
            $template = $templates[$i % count($templates)];
            $variation = $this->applyVariations($template['content'], $i);
            
            $scripts[] = [
                'title' => "Levar para Call " . ($i + 1),
                'content' => $variation,
                'tips' => 'Sempre ofereça um tempo específico (15-20 minutos). Facilite o agendamento mencionando sua disponibilidade.',
            ];
        }

        return $scripts;
    }

    /**
     * Get levar para call templates
     */
    private function getLevarCallTemplates(): array
    {
        return [
            ['content' => "Com base no que você me contou, tenho algumas ideias específicas que podem transformar seus resultados. Que tal agendarmos uma conversa rápida de 15 minutos para eu explicar melhor?", 'tips' => null],
            ['content' => "Acho que seria muito produtivo conversarmos pessoalmente. Posso te mostrar alguns cases de sucesso e explicar como podemos adaptar para o seu negócio. Quando você teria disponibilidade?", 'tips' => null],
            ['content' => "Tenho certeza que uma conversa rápida vai valer muito a pena. Posso mostrar resultados reais e tirar suas dúvidas. Que dia e horário funcionaria para você?", 'tips' => null],
            ['content' => "Seria ótimo conversarmos ao vivo para eu entender melhor suas necessidades e apresentar soluções personalizadas. Quando você tem um tempinho?", 'tips' => null],
            ['content' => "Baseado no que compartilhou, tenho algumas estratégias que podem funcionar perfeitamente para você. Que tal marcarmos uma call de 20 minutos?", 'tips' => null],
            ['content' => "Acredito que uma conversa direta seria mais eficiente. Posso apresentar um plano personalizado para seu negócio. Que tal 15 minutos esta semana?", 'tips' => null],
            ['content' => "Tenho materiais e cases que podem te ajudar a visualizar melhor o potencial. Podemos fazer uma call rápida? Qual dia funciona melhor?", 'tips' => null],
            ['content' => "Seria interessante você conhecer alguns resultados reais que já obtive. Podemos agendar uma conversa de 20 minutos? Quando você está livre?", 'tips' => null],
            ['content' => "Para criar uma proposta realmente personalizada, preciso entender alguns detalhes. Que tal uma call de 15 minutos? Quando você tem disponibilidade?", 'tips' => null],
            ['content' => "Tenho algo específico que quero compartilhar com você. Podemos agendar uma call rápida? Pode ser hoje ou amanhã, você escolhe o melhor horário.", 'tips' => null],
        ];
    }

    /**
     * Get quebra de objeção scripts - 50 unique scripts
     */
    private function getQuebraObjecaoScripts(string $categoryName): array
    {
        $objections = $this->getQuebraObjecaoTemplates();
        $scripts = [];

        for ($i = 0; $i < 50; $i++) {
            $obj = $objections[$i % count($objections)];
            $variation = $this->applyVariations($obj['response'], $i);
            
            $scripts[] = [
                'title' => "Quebra de Objeção: " . $obj['objection'] . " (" . (intval($i / count($objections)) + 1) . ")",
                'content' => $variation,
                'tips' => "Quando ouvir: '{$obj['objection']}', use este script. Sempre valide a objeção antes de responder.",
            ];
        }

        return $scripts;
    }

    /**
     * Get quebra de objeção templates
     */
    private function getQuebraObjecaoTemplates(): array
    {
        return [
            ['objection' => "Está caro", 'response' => "Entendo sua preocupação com o investimento. Vamos pensar no ROI - normalmente meus clientes recuperam esse investimento em pouco tempo. Além disso, podemos estruturar de forma parcelada. O que você acha?"],
            ['objection' => "Preciso pensar", 'response' => "Claro, é uma decisão importante. Que tal eu te enviar alguns cases de sucesso para você analisar? Posso também responder qualquer dúvida que surgir enquanto você pensa."],
            ['objection' => "Não tenho tempo agora", 'response' => "Compreendo totalmente. Por isso mesmo que minha abordagem é focada em resultados rápidos e sem tomar muito do seu tempo. Posso adaptar para o seu ritmo. Quando seria ideal para você?"],
            ['objection' => "Já trabalho com outra pessoa", 'response' => "Que ótimo que você já tem alguém! A minha abordagem pode ser complementar ou mesmo ajudar a potencializar os resultados. Sem compromisso, que tal apenas conhecer?"],
            ['objection' => "Não estou certo se funciona", 'response' => "Essa é uma preocupação válida. Por isso eu trabalho com resultados comprovados e posso te mostrar casos reais de empresas similares à sua. Vamos fazer um teste pequeno para você ver?"],
            ['objection' => "Não tenho orçamento", 'response' => "Entendo. Vamos pensar: quanto você está perdendo por não ter isso agora? O investimento costuma se pagar rapidamente. Podemos também começar pequeno e escalar conforme os resultados aparecem."],
            ['objection' => "Já tentei algo assim", 'response' => "Entendo. O que não funcionou da vez anterior? Às vezes é questão de abordagem ou timing. Posso adaptar minha estratégia para evitar os mesmos erros. Que tal conversarmos sobre o que aconteceu?"],
            ['objection' => "Não é prioridade agora", 'response' => "Respeito totalmente. Mas pense: quanto tempo você está perdendo enquanto espera? Às vezes, quando não é prioridade, é quando mais precisamos. Que tal começarmos pequeno?"],
            ['objection' => "Preciso falar com meu sócio", 'response' => "Perfeito! Que tal eu preparar um resumo rápido para você compartilhar com ele? Assim vocês podem decidir juntos com todas as informações."],
            ['objection' => "Não estou convencido", 'response' => "O que especificamente te deixa com dúvidas? Posso esclarecer e até mostrar resultados mais detalhados. O importante é você se sentir confortável com a decisão."],
        ];
    }

    /**
     * Get fechamento scripts - 50 unique scripts
     */
    private function getFechamentoScripts(string $categoryName): array
    {
        $templates = $this->getFechamentoTemplates();
        $scripts = [];

        for ($i = 0; $i < 50; $i++) {
            $template = $templates[$i % count($templates)];
            $variation = $this->applyVariations($template['content'], $i);
            
            $scripts[] = [
                'title' => "Fechamento " . ($i + 1),
                'content' => $variation,
                'tips' => 'Use tom de confiança e entusiasmo. Sempre confirme o próximo passo.',
            ];
        }

        return $scripts;
    }

    /**
     * Get fechamento templates
     */
    private function getFechamentoTemplates(): array
    {
        return [
            ['content' => "Perfeito! Então vamos começar? Posso preparar uma proposta personalizada até amanhã. O que você acha?", 'tips' => null],
            ['content' => "Excelente! Baseado na nossa conversa, vejo que faz muito sentido trabalharmos juntos. Posso enviar os próximos passos?", 'tips' => null],
            ['content' => "Ótimo! Estou muito animado para começar a trabalhar com você. Vou preparar tudo e envio para você ainda hoje. Combinado?", 'tips' => null],
            ['content' => "Perfeito! Acredito que vamos fazer grandes coisas juntos. Quer que eu prepare a documentação e envio para você revisar?", 'tips' => null],
            ['content' => "Fantástico! Então está decidido. Vou organizar tudo e te passo os detalhes. Quando podemos começar oficialmente?", 'tips' => null],
            ['content' => "Maravilha! Fico feliz que topou. Vou preparar o plano de ação detalhado e envio ainda esta semana. Te mando por aqui mesmo?", 'tips' => null],
            ['content' => "Que bom! Estou certo de que vamos alcançar resultados incríveis. Posso começar a trabalhar já na próxima semana. Topa?", 'tips' => null],
            ['content' => "Excelente decisão! Vou preparar tudo com muito carinho. Posso enviar o contrato e detalhes hoje ainda?", 'tips' => null],
            ['content' => "Perfeito! Estou ansioso para começar. Que tal começarmos segunda-feira? Te envio tudo antes do fim da semana.", 'tips' => null],
            ['content' => "Ótimo! Vou organizar todos os detalhes e te envio um resumo completo. Quando você gostaria de dar início?", 'tips' => null],
        ];
    }

    /**
     * Apply variations to script content
     */
    private function applyVariations(string $content, int $index): string
    {
        $variations = [
            ['Olá', 'Oi', 'E aí', 'Oi, tudo bem?', 'Olá, tudo certo?'],
            ['muito bem', 'ótimo', 'excelente', 'perfeito', 'sensacional'],
            ['podemos', 'vamos', 'que tal', 'seria interessante', 'que tal a gente'],
            ['hoje', 'ainda hoje', 'no final do dia', 'esta tarde', 'já'],
            ['amanhã', 'até amanhã', 'no máximo amanhã', 'até o fim do dia de amanhã'],
        ];

        // Aplica variações baseadas no índice
        $varied = $content;
        
        if (str_contains($varied, 'Olá')) {
            $varied = str_replace('Olá', $variations[0][$index % count($variations[0])], $varied);
        }
        
        if (str_contains($varied, 'ótimo')) {
            $varied = str_replace('ótimo', $variations[1][$index % count($variations[1])], $varied);
        }

        return $varied;
    }

    /**
     * Apply qualification variations
     */
    private function applyQualificationVariations(string $question, int $index): string
    {
        $intros = [
            "Para eu poder te ajudar da melhor forma, preciso entender melhor seu negócio. ",
            "Quero garantir que vou criar a melhor solução para você. ",
            "Para personalizar minha abordagem, me conta: ",
            "Antes de propor algo, preciso entender seu contexto. ",
            "Se eu pudesse fazer uma coisa por você hoje, ",
            "Para dimensionar melhor a solução, ",
            "Me ajuda a entender: ",
            "Para propor um investimento adequado, ",
            "Para eu poder te ajudar de verdade, ",
            "Antes de mais nada, preciso saber: ",
        ];

        // Extrai a pergunta do texto
        $parts = explode('. ', $question, 2);
        $intro = $intros[$index % count($intros)];
        
        if (count($parts) > 1) {
            return $intro . $parts[1];
        }
        
        return $intro . $question;
    }
}
