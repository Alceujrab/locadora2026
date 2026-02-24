<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\Vehicle;
use App\Models\Post;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Gera automaticamente o arquivo sitemap.xml para os motores de busca (SEO)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $sitemap = Sitemap::create(config('app.url'))
            ->add(Url::create('/')->setPriority(1.0)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY))
            ->add(Url::create('/frota')->setPriority(0.9)->setChangeFrequency(Url::CHANGE_FREQUENCY_DAILY));

        // Veículos Destaques ou Ativos
        Vehicle::all()->each(function (Vehicle $vehicle) use ($sitemap) {
            $sitemap->add(
                Url::create("/veiculo/{$vehicle->slug}")
                    ->setLastModificationDate($vehicle->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_WEEKLY)
                    ->setPriority(0.8)
            );
        });

        // Artigos do Blog Ativos
        Post::where('is_published', true)->get()->each(function (Post $post) use ($sitemap) {
            $sitemap->add(
                Url::create("/blog/{$post->slug}")
                    ->setLastModificationDate($post->updated_at)
                    ->setChangeFrequency(Url::CHANGE_FREQUENCY_MONTHLY)
                    ->setPriority(0.7)
            );
        });

        $sitemap->writeToFile(public_path('sitemap.xml'));

        $this->info('O Sitemap da Locadora foi gerado com sucesso em public/sitemap.xml');
    }
}
