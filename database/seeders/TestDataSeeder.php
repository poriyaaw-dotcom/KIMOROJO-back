<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Collection;
use App\Models\Product;

class TestDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create the 4 Main Categories
        $categoriesData = [
            ['name_en' => 'Rings', 'name_fa' => 'انگشترها', 'slug' => 'rings'],
            ['name_en' => 'Bracelets', 'name_fa' => 'دستبندها', 'slug' => 'bracelets'],
            ['name_en' => 'Necklaces', 'name_fa' => 'گردنبندها', 'slug' => 'necklaces'],
            ['name_en' => 'Earrings', 'name_fa' => 'گوشواره‌ها', 'slug' => 'earrings'],
        ];

        $categoryModels = [];
        foreach ($categoriesData as $cat) {
            $categoryModels[$cat['slug']] = Category::create($cat);
        }

        // 2. Create the 8 Collections
        $collectionsData = [
            ['name_en' => 'Timeless Elegance', 'name_fa' => 'ظرافت بی‌پایان', 'slug' => 'timeless-elegance'],
            ['name_en' => 'Modern Minimalist', 'name_fa' => 'مینیمال مدرن', 'slug' => 'modern-minimalist'],
            ['name_en' => 'Royal Heritage', 'name_fa' => 'میراث سلطنتی', 'slug' => 'royal-heritage'],
            ['name_en' => 'Bohemian Spirit', 'name_fa' => 'روح بوهیمین', 'slug' => 'bohemian-spirit'],
            ['name_en' => 'Celestial Dreams', 'name_fa' => 'رویاهای آسمانی', 'slug' => 'celestial-dreams'],
            ['name_en' => 'Ocean Breeze', 'name_fa' => 'نسیم اقیانوس', 'slug' => 'ocean-breeze'],
            ['name_en' => 'Urban Edge', 'name_fa' => 'لبه شهری', 'slug' => 'urban-edge'],
            ['name_en' => 'Vintage Charm', 'name_fa' => 'جذابیت وینتیج', 'slug' => 'vintage-charm'],
        ];

        $collectionModels = [];
        foreach ($collectionsData as $col) {
            $collectionModels[] = Collection::create($col);
        }

        // 3. Define 40 Products (10 per category)
        $productsData = [
            // 10 Rings
            ['Classic Gold Band', 'حلقه طلای کلاسیک', 'rings'],
            ['Silver Solitaire', 'تک نگین نقره', 'rings'],
            ['Diamond Eternity Ring', 'حلقه ابدیت الماس', 'rings'],
            ['Rose Gold Twist', 'پیچ طلای رز', 'rings'],
            ['Sapphire Statement', 'یاقوت کبود خاص', 'rings'],
            ['Minimalist Silver Band', 'حلقه نقره مینیمال', 'rings'],
            ['Emerald Cut Ring', 'انگشتر زمرد', 'rings'],
            ['Ruby Passion Ring', 'انگشتر یاقوت سرخ', 'rings'],
            ['Pearl Accent Ring', 'انگشتر مروارید', 'rings'],
            ['Vintage Gold Signet', 'انگشتر مهر طلای وینتیج', 'rings'],
            
            // 10 Bracelets
            ['Chain Link Bracelet', 'دستبند زنجیری', 'bracelets'],
            ['Tennis Bracelet', 'دستبند تنیس', 'bracelets'],
            ['Charm Bracelet', 'دستبند آویزدار', 'bracelets'],
            ['Bangle Bracelet', 'دستبند النگو', 'bracelets'],
            ['Cuff Bracelet', 'دستبند کاف', 'bracelets'],
            ['Silver Chain Bracelet', 'دستبند زنجیر نقره', 'bracelets'],
            ['Gold Beaded Bracelet', 'دستبند مهره‌ای طلا', 'bracelets'],
            ['Leather and Gold Bracelet', 'دستبند چرم و طلا', 'bracelets'],
            ['Diamond Tennis Bracelet', 'دستبند تنیس الماس', 'bracelets'],
            ['Pearl Strand Bracelet', 'دستبند رشته مروارید', 'bracelets'],
            
            // 10 Necklaces
            ['Pearl Pendant Necklace', 'گردنبند آویز مروارید', 'necklaces'],
            ['Diamond Choker', 'چوکر الماس', 'necklaces'],
            ['Gold Chain Necklace', 'گردنبند زنجیر طلا', 'necklaces'],
            ['Locket Necklace', 'گردنبند پلاک', 'necklaces'],
            ['Statement Necklace', 'گردنبند خاص', 'necklaces'],
            ['Silver Pendant Necklace', 'گردنبند آویز نقره', 'necklaces'],
            ['Layered Gold Necklace', 'گردنبند لایه‌ای طلا', 'necklaces'],
            ['Gemstone Necklace', 'گردنبند سنگ قیمتی', 'necklaces'],
            ['Cross Pendant Necklace', 'گردنبند آویز صلیب', 'necklaces'],
            ['Heart Locket Necklace', 'گردنبند پلاک قلب', 'necklaces'],
            
            // 10 Earrings
            ['Gold Hoop Earrings', 'گوشواره حلقه‌ای طلا', 'earrings'],
            ['Silver Stud Earrings', 'گوشواره میخی نقره', 'earrings'],
            ['Diamond Drop Earrings', 'گوشواره آویز الماس', 'earrings'],
            ['Pearl Drop Earrings', 'گوشواره آویز مروارید', 'earrings'],
            ['Chandelier Earrings', 'گوشواره لوستی', 'earrings'],
            ['Huggie Hoop Earrings', 'گوشواره حلقه‌ای کوچک', 'earrings'],
            ['Tassel Earrings', 'گوشواره ریشه‌دار', 'earrings'],
            ['Geometric Earrings', 'گوشواره هندسی', 'earrings'],
            ['Crystal Stud Earrings', 'گوشواره میخی کریستال', 'earrings'],
            ['Vintage Drop Earrings', 'گوشواره آویز وینتیج', 'earrings'],
        ];

        // 4. Insert Products into Database
        foreach ($productsData as $index => $data) {
            $categorySlug = $data[2];
            $category = $categoryModels[$categorySlug];
            
            // Distribute evenly: 40 products / 8 collections = 5 products per collection
            $collection = $collectionModels[$index % 8]; 

            Product::create([
                'name_en' => $data[0],
                'name_fa' => $data[1],
                'description_en' => "Beautiful handcrafted {$data[0]}.",
                'description_fa' => "{$data[1]} دست‌ساز و زیبا.",
                'weight' => rand(200, 1500) / 100, // Random weight between 2.00g and 15.00g
                'purity' => rand(0, 1) ? '18k' : '925',
                'metal_type' => rand(0, 1) ? 'gold' : 'silver',
                'making_charges' => rand(500000, 2000000), // Random making charges
                'stock' => rand(1, 10),
                'images' => [], // Empty array (No pictures, you will add them manually)
                'category_id' => $category->id,
                'collection_id' => $collection->id,
            ]);
        }
    }
}