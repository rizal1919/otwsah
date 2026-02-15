<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>The Wedding of <?= $inv['nama_pria'] ?> & <?= $inv['nama_wanita'] ?></title>
    
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@300;400&family=Playfair+Display:ital,wght@0,400;0,700;1,400&display=swap" rel="stylesheet">
    
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --primary-color: #8B4513; /* Coklat Batik */
            --accent-color: #DAA520; /* Emas */
            --bg-color: #faf9f6; /* Cream */
        }

        body {
            font-family: 'Lato', sans-serif;
            background-color: var(--bg-color);
            color: #333;
            overflow-x: hidden;
        }

        h1, h2, h3 {
            font-family: 'Playfair Display', serif;
            color: var(--primary-color);
        }

        /* --- BATIK PATTERN DECORATION --- */
        .batik-border-top {
            width: 100%;
            height: 20px;
            background: repeating-linear-gradient(45deg, #8B4513, #8B4513 10px, #DAA520 10px, #DAA520 20px);
            margin-bottom: 30px;
        }

        /* --- HERO SECTION --- */
        .hero {
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            background: linear-gradient(rgba(0,0,0,0.5), rgba(0,0,0,0.5)), url('https://images.unsplash.com/photo-1606216794074-735e91aa2c92?q=80&w=1000&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 20px;
        }

        .hero h1 { color: #fff; font-size: 3rem; margin-bottom: 10px; }
        .hero p { font-size: 1.2rem; letter-spacing: 2px; text-transform: uppercase; border-top: 2px solid var(--accent-color); border-bottom: 2px solid var(--accent-color); padding: 10px 0; display: inline-block; }

        /* --- SECTIONS --- */
        .section { padding: 80px 20px; text-align: center; position: relative; }
        .ornamen { color: var(--accent-color); font-size: 2rem; margin-bottom: 20px; }
        
        .couple-img {
            width: 150px; height: 150px; object-fit: cover;
            border-radius: 50%; border: 3px solid var(--accent-color);
            margin-bottom: 15px; padding: 5px; background: white;
        }

        .card-acara {
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border-top: 5px solid var(--primary-color);
        }

        .btn-maps {
            background-color: var(--primary-color); color: white;
            border-radius: 50px; padding: 10px 30px; text-decoration: none;
            transition: 0.3s;
        }
        .btn-maps:hover { background-color: var(--accent-color); color: white; }

        /* Footer */
        footer { background: #333; color: #aaa; padding: 20px; text-align: center; font-size: 0.8rem; }
    </style>
</head>
<body>

    <header class="hero">
        <div data-aos="fade-down" data-aos-duration="1500">
            <p>The Wedding Of</p>
            <h1><?= $inv['nama_pria'] ?> & <?= $inv['nama_wanita'] ?></h1>
            <p style="border:none; font-size: 1rem; text-transform: capitalize;">
                <?= date('d . m . Y', strtotime($inv['tgl_resepsi'])) ?>
            </p>
        </div>
    </header>

    <div class="batik-border-top"></div>

    <section class="section" id="mempelai">
        <div class="container">
            <div class="ornamen" data-aos="zoom-in">⚜</div>
            <h2 data-aos="fade-up">Pasangan Mempelai</h2>
            <p class="mb-5" data-aos="fade-up" data-aos-delay="100">Maha Suci Allah yang telah menciptakan makhluk-Nya berpasang-pasangan.</p>

            <div class="row justify-content-center align-items-center g-5">
                <div class="col-md-5" data-aos="fade-right">
                    <img src="https://via.placeholder.com/150" class="couple-img" alt="Pria">
                    <h3 class="mt-3"><?= $inv['nama_pria_lengkap'] ?></h3>
                    <p>Putra dari Bpk. <?= $inv['ortu_pria_ayah'] ?> <br>& Ibu <?= $inv['ortu_pria_ibu'] ?></p>
                </div>
                
                <div class="col-md-2" data-aos="zoom-in">
                    <h1 style="font-size: 4rem; color: var(--accent-color);">&</h1>
                </div>

                <div class="col-md-5" data-aos="fade-left">
                    <img src="https://via.placeholder.com/150" class="couple-img" alt="Wanita">
                    <h3 class="mt-3"><?= $inv['nama_wanita_lengkap'] ?></h3>
                    <p>Putri dari Bpk. <?= $inv['ortu_wanita_ayah'] ?> <br>& Ibu <?= $inv['ortu_wanita_ibu'] ?></p>
                </div>
            </div>
        </div>
    </section>

    <section class="section" style="background-color: #f3efe9;">
        <div class="container">
            <div class="ornamen" data-aos="zoom-in">⚜</div>
            <h2 class="mb-5" data-aos="fade-up">Rangkaian Acara</h2>
            
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card-acara" data-aos="flip-up">
                        <h3>Resepsi Pernikahan</h3>
                        <hr style="width: 50px; margin: 20px auto; border: 2px solid var(--accent-color); opacity: 1;">
                        
                        <p class="fw-bold mb-0">Tanggal:</p>
                        <p><?= date('l, d F Y', strtotime($inv['tgl_resepsi'])) ?></p>

                        <p class="fw-bold mb-0">Lokasi:</p>
                        <p>Kediaman Mempelai Wanita<br>Jl. Kebahagiaan No. 1</p>
                        
                        <a href="#" class="btn-maps mt-3">Lihat Lokasi</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <footer>
        <p>Created with Love by OtwSah</p>
    </footer>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({
            once: true, // Animasi hanya sekali saat scroll ke bawah
            duration: 1000, // Durasi animasi 1 detik
        });
    </script>
</body>
</html>