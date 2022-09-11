<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Color;
use App\Entity\Price;
use App\Entity\Reference;
use App\Entity\Size;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Faker\Factory::create();

        $colors = ['noir', 'blanc', 'bleu', 'jaune', 'rouge', 'vert'];
        $dataColors = [];
        for ($i = 0; $i < count($colors); $i++) {
            $color = new Color();
            $color->setName($colors[$i]);
            $dataColors[] = $color;
            $manager->persist($color);
        }

        $sizes = ['xs', 's', 'm', 'l', 'xl'];
        $dataSizes = [];
        for ($i = 0; $i < count($sizes); $i++) {
            $size = new Size();
            $size->setName($sizes[$i]);
            $dataSizes[] = $size;
            $manager->persist($size);
        }

        $prices = [29, 39, 49];
        $dataPrices = [];
        for ($i = 0; $i < count($prices); $i++) {
            $price = new Price();
            $price->setAmount($prices[$i]);
            $dataPrices[] = $price;
            $manager->persist($price);
        }

        $titles = ['Dahu', 'Seoul', 'Auburn'];
        $images = [
            'https://thumbs.dreamstime.com/b/la-mode-v%C3%AAtx-l-illustration-bleue-de-forme-de-t-shirt-8229384.jpg',
            'https://img.myloview.fr/images/illustration-unique-de-vecteur-de-dessin-anime-t-shirt-bleu-700-145918035.jpg',
            'https://previews.123rf.com/images/siberica/siberica1601/siberica160100173/51442205-t-shirt-croquis-homme-isol%C3%A9-sur-fond-blanc-vector-illustration-.jpg'
        ];
        $dataTitles = [];
        for ($i = 0; $i < count($titles); $i++) {
            $title = new Reference();
            $title
                ->setTitle($titles[$i])
                ->setSlug(strtolower($titles[$i]))
                ->setImage($images[$i])
                ->setPrice($dataPrices[$i])
                ->setDescription(implode(' ', $faker->sentences($faker->randomDigitNotNull())));
            $dataTitles[] = $title;
            $manager->persist($title);
        }

        for ($i = 0; $i < 15; $i++) {
            $article = new Article();
            $article
                ->setColor($faker->randomElement($dataColors))
                ->setSize($faker->randomElement($dataSizes))
                ->setQty($faker->randomDigitNotNull())
                ->setTitle($faker->randomElement($dataTitles));
            $manager->persist($article);
        }

        $manager->flush();
    }
}
