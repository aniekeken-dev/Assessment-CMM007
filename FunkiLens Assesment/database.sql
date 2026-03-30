CREATE DATABASE IF NOT EXISTS FunkiLens_db;
USE FunkiLens_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'user') NOT NULL DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS equipment (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150)  NOT NULL,
    category VARCHAR(100)  NOT NULL,   -- e.g. Camera, Lens, Drone, Studio Light
    description TEXT,
    serial_number VARCHAR(100)  UNIQUE,
    equipment_condition ENUM('Excellent', 'Good', 'Fair', 'Poor') NOT NULL DEFAULT 'Good',
    rental_price DECIMAL(8,2)  NOT NULL DEFAULT 0.00,  -- price per day in £
    quantity  INT           NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS rentals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT  NOT NULL,
    equipment_id  INT  NOT NULL,
    quantity  INT  NOT NULL DEFAULT 1,
    rent_date  DATE NOT NULL,
    due_date   DATE NOT NULL,             
    status  ENUM('rented', 'returned') NOT NULL DEFAULT 'rented',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    -- Foreign keys linking rentals to users and equipment
    FOREIGN KEY (user_id) REFERENCES users(id)      ON DELETE CASCADE,
    FOREIGN KEY (equipment_id) REFERENCES equipment(id)  ON DELETE CASCADE
);

INSERT INTO users (name, email, password, role) VALUES
('Admin User', 'admin@funkilens.com', 'funikilenss', 'admin');

INSERT INTO equipment (name, category, description, serial_number, equipment_condition, rental_price, quantity) VALUES
('Sony Alpha A7 IV', 'Camera', 'Full-frame mirrorless camera with 33MP sensor, 4K video, and excellent low-light performance.', 'CAM-001-SONY', 'Excellent', 29.99, 3),
('Canon EOS R5', 'Camera', 'High-resolution full-frame mirrorless camera with 45MP sensor and 8K RAW video recording.', 'CAM-002-CANON', 'Excellent', 34.99, 2),
('Nikon Z6 II', 'Camera', 'Versatile full-frame mirrorless camera ideal for photography and video with dual card slots.', 'CAM-003-NIKON', 'Good', 24.99, 3),
('Fujifilm X-T5', 'Camera', 'Compact APS-C mirrorless camera with 40MP sensor and classic film simulation modes.', 'CAM-004-FUJI', 'Excellent', 22.99, 2),
('Sony ZV-E10', 'Camera', 'Lightweight APS-C mirrorless camera designed for vloggers and content creators.', 'CAM-005-SONY', 'Good', 14.99, 4),
('Canon EOS 90D', 'Camera', 'Versatile DSLR with 32.5MP APS-C sensor, fast autofocus, and 4K video capability.', 'CAM-006-CANON', 'Good', 19.99, 2),
('Panasonic Lumix GH6', 'Camera', 'Micro Four Thirds mirrorless camera with 25MP sensor and C4K 60fps video recording.', 'CAM-007-PANA', 'Excellent', 27.99, 2),
('Blackmagic Pocket Cinema 6K', 'Camera', 'Professional cinema camera with 6K sensor, Super 35 format, and RAW recording.', 'CAM-008-BMD', 'Good', 39.99, 1),
('Sony Alpha A6400', 'Camera', 'APS-C mirrorless camera with real-time Eye AF and 180-degree tilting touchscreen.', 'CAM-009-SONY', 'Good', 17.99, 3),
('OM System OM-5', 'Camera', 'Compact weatherproof Micro Four Thirds camera, great for outdoor and adventure shoots.', 'CAM-010-OM', 'Excellent', 21.99, 2);

INSERT INTO equipment (name, category, description, serial_number, equipment_condition, rental_price, quantity) VALUES
('Sony FE 24-70mm f/2.8 GM', 'Camera Lens', 'Professional standard zoom lens with constant f/2.8 aperture for Sony E-mount cameras.', 'LNS-001-SONY', 'Excellent', 12.99, 2),
('Canon RF 50mm f/1.2L USM', 'Camera Lens', 'Premium nifty-fifty prime lens with ultra-fast aperture for stunning portrait bokeh.', 'LNS-002-CANON', 'Excellent', 14.99, 2),
('Sigma 35mm f/1.4 DG DN Art', 'Camera Lens', 'Sharp wide-angle prime lens compatible with Sony E and L-mount, ideal for street photography.', 'LNS-003-SIGMA', 'Good', 9.99, 3),
('Tamron 17-28mm f/2.8 Di III RXD', 'Camera Lens', 'Compact wide-angle zoom for Sony E-mount, excellent for landscapes and architecture.', 'LNS-004-TAMRON', 'Good', 10.99, 2),
('Nikon Z 85mm f/1.8 S', 'Camera Lens', 'Exceptional portrait prime lens for Nikon Z-mount with beautiful subject separation.', 'LNS-005-NIKON', 'Excellent', 11.99, 2),
('Sony FE 70-200mm f/2.8 GM II', 'Camera Lens', 'Lightweight telephoto zoom with advanced autofocus, ideal for sports and wildlife.', 'LNS-006-SONY', 'Excellent', 18.99, 1),
('Canon EF 100mm f/2.8L Macro IS USM', 'Camera Lens', 'Versatile macro lens with image stabilisation, also great for close-up portraits.', 'LNS-007-CANON', 'Good', 10.99, 2),
('Fujifilm XF 16-55mm f/2.8 R LM WR', 'Camera Lens', 'Weather-resistant standard zoom lens for Fujifilm X-mount with constant f/2.8 aperture.', 'LNS-008-FUJI', 'Good', 11.99, 2),
('Sigma 14mm f/1.8 DG HSM Art', 'Camera Lens', 'Ultra-wide prime lens superb for astrophotography and dramatic landscape shots.', 'LNS-009-SIGMA', 'Good', 13.99, 2),
('Laowa 25mm f/2.8 2.5-5X Ultra Macro', 'Camera Lens', 'Specialist ultra-macro lens for capturing extreme close-up details in product photography.', 'LNS-010-LAOWA', 'Excellent', 8.99, 2);

INSERT INTO equipment (name, category, description, serial_number, equipment_condition, rental_price, quantity) VALUES
('DJI Mavic 3 Pro', 'Drone', 'Flagship consumer drone with triple-camera Hasselblad system and 46-min flight time.', 'DRN-001-DJI', 'Excellent', 49.99, 2),
('DJI Air 3', 'Drone', 'Compact dual-camera drone with 48MP sensors and omnidirectional obstacle sensing.', 'DRN-002-DJI', 'Excellent', 39.99, 3),
('DJI Mini 4 Pro', 'Drone', 'Sub-250g foldable drone with 4K 60fps video and omnidirectional obstacle avoidance.', 'DRN-003-DJI', 'Excellent', 29.99, 3),
('Autel EVO Lite+', 'Drone', 'Versatile drone with 1-inch CMOS sensor, 6K video, and adjustable aperture.', 'DRN-004-AUTEL', 'Good', 34.99, 2),
('DJI Inspire 3', 'Drone', 'Professional cinema-grade drone supporting 8K RAW video, designed for film productions.', 'DRN-005-DJI', 'Excellent', 149.99, 1),
('Skydio 2+', 'Drone', 'American-made autonomous drone with industry-leading AI-powered obstacle avoidance.', 'DRN-006-SKYDIO', 'Good', 37.99, 2),
('DJI FPV Combo', 'Drone', 'First-person view drone delivering an immersive flying experience at up to 140 km/h.', 'DRN-007-DJI', 'Good', 32.99, 2),
('Parrot Anafi USA', 'Drone', 'Enterprise drone with thermal camera, 32x zoom, and NDAA-compliant design.', 'DRN-008-PARROT', 'Good', 59.99, 1),
('Yuneec Typhoon H3', 'Drone', 'Hexacopter drone with retractable landing gear and Leica-certified compact camera.', 'DRN-009-YUNEEC', 'Fair', 27.99, 1),
('DJI Agras T40', 'Drone', 'Agricultural spraying drone with 40kg payload and phased-array radar for terrain following.', 'DRN-010-DJI', 'Good', 99.99, 1);

INSERT INTO equipment (name, category, description, serial_number, equipment_condition, rental_price, quantity) VALUES
('Godox AD600Pro', 'Studio Light', 'Powerful 600W outdoor flash strobe with TTL, HSS, and built-in 2.4G wireless system.', 'LGT-001-GODOX', 'Excellent', 19.99, 3),
('Aputure 600d Pro', 'Studio Light', 'Professional 600W daylight LED fixture with CRI 96+ and Bowens mount compatibility.', 'LGT-002-APUTURE', 'Excellent', 24.99, 2),
('Profoto B10 Plus', 'Studio Light', 'Compact 500Ws portable studio light with built-in TTL and high-speed sync support.', 'LGT-003-PROFOTO', 'Excellent', 29.99, 2),
('Godox SL-60W', 'Studio Light', 'Affordable 60W continuous LED video light, ideal for beginner YouTube studios.', 'LGT-004-GODOX', 'Good', 8.99, 5),
('Neewer 660 Pro RGB LED', 'Studio Light', 'RGB LED panel with full-colour control and CRI 97+ for creative lighting effects.', 'LGT-005-NEEWER', 'Good', 11.99, 4),
('Aputure MC Pro RGBWW', 'Studio Light', 'Pocket-sized RGBWW LED light with magnetic mounting and built-in battery.', 'LGT-006-APUTURE', 'Excellent', 9.99, 4),
('Westcott Rapid Box Switch Octa', 'Studio Light', 'Large 36-inch octabox softbox for creating beautiful soft wrap-around light.', 'LGT-007-WEST', 'Good', 6.99, 3),
('Elgato Key Light', 'Studio Light', 'Professional studio LED panel designed for streaming, webcam, and video calls.', 'LGT-008-ELGATO', 'Good', 7.99, 4),
('Nanlite Forza 500B', 'Studio Light', 'Powerful bi-colour LED fresnel light with 500W output and CCT range 2700-6500K.', 'LGT-009-NANLITE', 'Excellent', 22.99, 2),
('Savage Universal LED Ring Light', 'Studio Light', '18-inch ring light with phone holder and tripod, perfect for portraits and beauty shots.', 'LGT-010-SAVAGE', 'Good', 6.99, 5);
