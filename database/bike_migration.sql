-- ==============================================================================
-- PakBikeParts - Motorcycle Specialized Database Migration & Seed
-- 100% Focused on Pakistani Bike Market: Honda CD70/CG125, Yamaha YBR, Suzuki GS150
-- ==============================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- 1. TRUNCATE CATALOG TABLES (Preserve Users & Orders)
TRUNCATE TABLE `product_fitment`;
TRUNCATE TABLE `product_price_tiers`;
TRUNCATE TABLE `product_images`;
TRUNCATE TABLE `reviews`;
TRUNCATE TABLE `products`;
TRUNCATE TABLE `categories`;
TRUNCATE TABLE `brands`;
TRUNCATE TABLE `vehicles`;

-- 2. BIKE CATEGORIES & SYSTEMS
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `sort_order`, `is_active`) VALUES
(1, 'Engine & Cylinder Parts', 'engine-cylinder', 'Piston kits, cylinder blocks, clutch plates, valves, gaskets, and timing chains for bikes.', 1, 1),
(2, 'Carburetor & Fuel System', 'carburetor-fuel', 'Complete carburetors, repair jet kits, petrol tanks, and fuel petcocks.', 2, 1),
(3, 'Drive & Sprockets', 'drive-transmission', 'Heavy-duty 428H chain & sprocket kits, front sprockets, and gear shift forks.', 3, 1),
(4, 'Electrical & Ignition', 'electrical-ignition', 'CDI units, magneto coils, spark plugs, rectifiers, and complete wiring harnesses.', 4, 1),
(5, 'Brakes & Control Cables', 'brakes-cables', 'Front & rear brake shoes, disc pads, clutch cables, and throttle cables.', 5, 1),
(6, 'Shocks & Suspension', 'suspension-handling', 'Front hydraulic forks, rear shock absorbers, steering cone sets, and swingarms.', 6, 1),
(7, 'Body Covers, Lights & Seats', 'body-plastics-lights', 'Side covers (tappay), headlights, tail lamps, indicators, mudguards, and seats.', 7, 1),
(8, 'Silencers & Exhaust', 'silencers-exhaust', 'Original stock exhaust silencers (dholki), exhaust bends, and chrome heat guards.', 8, 1),
(9, 'Tyres, Tubes & 4T Oils', 'tyres-tubes-oils', 'Tubeless tyres (2.50-17, 2.75-17, 90/90-18), heavy-duty butyl tubes, and 4T engine oils.', 9, 1);

-- 3. MOTORCYCLE BRANDS (Pakistan Market)
INSERT INTO `brands` (`id`, `name`, `slug`, `description`, `is_popular`) VALUES
(1, 'Atlas Honda Genuine', 'atlas-honda', 'Official 100% genuine replacement parts engineered by Atlas Honda Pakistan.', 1),
(2, 'Crown Lifan', 'crown-lifan', 'Pakistan\'s premier and most trusted motorcycle aftermarket spare parts brand.', 1),
(3, 'Yamaha Genuine (YGP)', 'yamaha-genuine', 'Original factory-certified components for Yamaha YBR and YB series.', 1),
(4, 'Suzuki Genuine (SGP)', 'suzuki-genuine', 'Authentic Pak Suzuki certified parts for GS150, GD110S, and GR150.', 1),
(5, 'FCC Clutch Japan', 'fcc-clutch', 'World-leading Japanese friction discs and heavy-duty clutch plates.', 1),
(6, 'NGK Spark Plugs', 'ngk', 'Japanese high-ignition spark plugs for maximum mileage and cold starting.', 1),
(7, 'Mikuni Japan', 'mikuni', 'Precision carburetors and fuel-air ratio systems engineered in Japan.', 1),
(8, 'Panther Tyres', 'panther-tyres', 'High-grip durable motorcycle tyres engineered for Pakistani road conditions.', 1),
(9, 'Servis Tyres', 'servis-tyres', 'Pakistan\'s oldest and most respected motorcycle tyre and tube manufacturer.', 1),
(10, 'Caltex Havoline', 'caltex-havoline', 'Premium 4T motorcycle lubricants and engine oils for 70cc to 150cc bikes.', 1);

-- 4. PAKISTANI MOTORCYCLE MAKES & MODELS
INSERT INTO `vehicles` (`id`, `make`, `model`, `year_start`, `year_end`, `engine`, `fuel_type`) VALUES
-- Honda
(1, 'Honda', 'CD 70', 2012, 2025, '72cc 4-Stroke OHC', 'Petrol'),
(2, 'Honda', 'CD 70 Dream', 2014, 2024, '72cc 4-Stroke OHC', 'Petrol'),
(3, 'Honda', 'Pridor 100', 2013, 2024, '97cc 4-Stroke OHC', 'Petrol'),
(4, 'Honda', 'CG 125 (Kick Start)', 2010, 2025, '124cc 4-Stroke OHV (Euro II)', 'Petrol'),
(5, 'Honda', 'CG 125 Self / Special Edition', 2019, 2025, '124cc 4-Stroke OHV 5-Speed', 'Petrol'),
(6, 'Honda', 'CB 125F', 2019, 2024, '124cc 4-Stroke OHV 5-Speed', 'Petrol'),
(7, 'Honda', 'CB 150F', 2017, 2024, '149cc 4-Stroke SOHC Balancer Engine', 'Petrol'),

-- Yamaha
(8, 'Yamaha', 'YBR 125', 2015, 2025, '124cc 4-Stroke SOHC Balancer', 'Petrol'),
(9, 'Yamaha', 'YBR 125G (Trail Edition)', 2016, 2025, '124cc 4-Stroke SOHC Balancer', 'Petrol'),
(10, 'Yamaha', 'YB 125Z', 2017, 2024, '124cc 4-Stroke SOHC 5-Speed', 'Petrol'),
(11, 'Yamaha', 'YB 125Z-DX (Disc Brake)', 2020, 2025, '124cc 4-Stroke SOHC Disc', 'Petrol'),

-- Suzuki
(12, 'Suzuki', 'GS 150', 2011, 2024, '150cc 4-Stroke SOHC 5-Speed', 'Petrol'),
(13, 'Suzuki', 'GS 150 SE (Special Edition)', 2013, 2024, '150cc Alloy Wheels 5-Speed', 'Petrol'),
(14, 'Suzuki', 'GD 110S', 2012, 2025, '113cc 4-Stroke SOHC Euro II', 'Petrol'),
(15, 'Suzuki', 'GR 150', 2018, 2025, '149cc 4-Stroke SOHC Balancer', 'Petrol'),

-- United & Chinese 70cc / 100cc / 125cc
(16, 'United', 'US 70', 2012, 2025, '72cc 4-Stroke OHC', 'Petrol'),
(17, 'United', 'US 125', 2016, 2024, '124cc 4-Stroke OHV', 'Petrol'),
(18, 'Road Prince', 'RP 70 Passion', 2012, 2025, '72cc 4-Stroke OHC', 'Petrol'),
(19, 'Road Prince', 'RP 110 Weego', 2015, 2023, '110cc 4-Stroke OHC', 'Petrol'),
(20, 'Hi-Speed', 'Infinity 150', 2017, 2024, '149cc Cafe Racer', 'Petrol');

-- 5. REALISTIC MOTORCYCLE SPARE PARTS (16 Essential Fast-Moving Items)
INSERT INTO `products` (`id`, `name`, `slug`, `sku`, `oem_number`, `cross_references`, `brand_id`, `category_id`, `condition_type`, `warranty`, `short_description`, `description`, `price`, `compare_at_price`, `cost_price`, `stock_quantity`, `low_stock_threshold`, `is_featured`, `is_active`, `weight_kg`, `meta_title`, `meta_description`) VALUES
(1, 'Atlas Honda CD70 Genuine Mikuni Carburetor Assembly', 'honda-cd70-genuine-carburetor', 'AHL-CRB-070', '16100-087-004', '16100-087-671, CD70-CRB-MIK', 1, 2, 'Genuine', '100% Genuine Atlas Honda Box', 'Original factory carburetor with precision brass jets, high fuel economy (60+ km/l), and smooth cold-start choke.', '<p>Restore maximum fuel economy and crisp throttle response with this 100% Genuine Atlas Honda CD70 Carburetor Assembly. Manufactured with precision Japanese Mikuni tooling, calibrated specifically for Pakistani 72cc 4-stroke engines and local fuel grades.</p><ul><li>Part Number: 16100-087-004</li><li>Compatible with Honda CD70 (all models), CD70 Dream, United 70, Road Prince 70</li><li>Includes pre-installed float, needle valve, main jet, and overflow hose</li><li>Factory tuned for optimal air-fuel ratio and easy kick-start</li></ul>', 4200.00, 4800.00, 3400.00, 35, 5, 1, 1, 0.65, 'Honda CD 70 Genuine Carburetor Price in Pakistan | PakBikeParts', 'Buy original Atlas Honda CD 70 carburetor in Pakistan. Best mileage, genuine hologram packaging, and fast COD delivery.'),

(2, 'Atlas Honda CG125 Heavy-Duty Clutch & Friction Plate Set (5 Pcs)', 'honda-cg125-clutch-plate-set', 'AHL-CLT-125', '22201-107-000', 'FCC-CG125-CL, 22201-KCS-920', 1, 1, 'Genuine', 'Atlas Honda Guaranteed', 'Original 5-piece fiber clutch disc set engineered for maximum torque transfer, zero slippage, and smooth gear shifts.', '<p>Prevent clutch slippage and engine power loss under heavy acceleration with the official Atlas Honda CG125 Clutch Plate Set. Constructed from heat-resistant organic friction material with reinforced steel core plates to handle aggressive Pakistani riding conditions.</p><ul><li>Part Number: 22201-107-000</li><li>Direct fit for Honda CG125, CG125 Special Edition, and China 125cc bikes</li><li>Complete set of 5 friction plates</li><li>High heat resistance with zero clutch drag or wheel burn</li></ul>', 2850.00, 3400.00, 2200.00, 50, 8, 1, 1, 0.45, 'Honda CG 125 Genuine Clutch Plates Price in Pakistan | PakBikeParts', 'Original Atlas Honda CG125 clutch plates set of 5. Best pickup, genuine packing, nationwide Cash on Delivery.'),

(3, 'Yamaha YBR 125 / YBR 125G Heavy-Duty 428H Chain & Sprocket Kit', 'yamaha-ybr125-chain-sprocket-kit', 'YGP-CHK-125', '5VL-E5381-00', '428H-118L, 14T-43T-YBR', 3, 3, 'Genuine', 'Yamaha Genuine Warranty', 'Heavy-duty 428H pre-lubricated drive chain with 14T front and 43T hardened rear sprockets for long life.', '<p>Eliminate chain slack and vibration on your Yamaha YBR. This complete drive chain & sprocket kit includes a heavy-duty 428H pre-stretched link chain paired with induction-hardened high-carbon steel sprockets for quiet, vibration-free highway cruising.</p><ul><li>Chain Spec: 428H Heavy-Duty (118 Links) with master link</li><li>Front Sprocket: 14 Teeth / Rear Sprocket: 43 Teeth</li><li>Direct fit for Yamaha YBR 125, YBR 125G, and YB 125Z</li><li>Up to 25,000 km operating lifespan with regular oiling</li></ul>', 5400.00, 6200.00, 4200.00, 20, 4, 1, 1, 1.85, 'Yamaha YBR 125 Chain Sprocket Kit Price in Pakistan | PakBikeParts', 'Buy original Yamaha YBR 125 / YBR 125G chain sprocket kit in Pakistan. Heavy-duty 428H drive kit with COD.'),

(4, 'Pak Suzuki GS150 OEM Digital CDI Ignition Unit', 'suzuki-gs150-cdi-unit', 'SGP-CDI-150', '32900-33G00', 'GS150-CDI-DIG, 32900-24B00', 4, 4, 'Genuine', 'Tested Suzuki Genuine', 'Microprocessor-controlled capacitive discharge ignition unit for instant revs, RPM advance, and reliable spark.', '<p>Fix RPM misfiring and high-speed hesitation on your Suzuki GS 150 with this genuine digital CDI box. Designed with built-in electronic timing advance curve to ensure optimal spark ignition across all throttle ranges.</p><ul><li>Part Number: 32900-33G00</li><li>Direct plug-and-play fitment for Suzuki GS150 and GS150 SE</li><li>Prevents engine missing above 7,000 RPM</li><li>Weatherproof sealed epoxy housing against rain and dust</li></ul>', 3600.00, 4200.00, 2700.00, 18, 3, 1, 1, 0.20, 'Suzuki GS 150 CDI Unit Price in Pakistan | PakBikeParts', 'Order original Pak Suzuki GS150 CDI ignition unit. Tested for instant ignition, genuine part, fast shipping.'),

(5, 'NGK Japan D8EA Copper Core Spark Plug (for Honda CG125 / GS150)', 'ngk-d8ea-spark-plug', 'NGK-SPK-D8EA', 'D8EA-2120', 'DR8EA, X24ES-U, 98069-58716', 6, 4, 'Genuine', '100% Original NGK Japan', 'Standard copper core heat range 8 spark plug for Honda CG125, Suzuki GS150, and 125cc-150cc motorcycles.', '<p>The most popular spark plug in Pakistan for 125cc and 150cc motorcycles. Features 99% pure alumina silicate ceramic insulator and trivalent metal plating for superior anti-corrosion and anti-seizing properties in hot summers.</p><ul><li>Thread Size: 12mm / Thread Reach: 19mm (3/4")</li><li>Hex Size: 18mm (11/16")</li><li>Heat Range: 8</li><li>Fitment: Honda CG125, CB150F, Suzuki GS150, Yamaha YB125Z</li></ul>', 450.00, 550.00, 320.00, 120, 20, 1, 1, 0.06, 'NGK D8EA Spark Plug Price in Pakistan for CG 125 & GS 150', 'Buy original NGK Japan D8EA spark plug for CG 125 and GS 150. Authentic hologram stamp with nationwide delivery.'),

(6, 'NGK Japan C7HSA Spark Plug (for Honda CD70 & China 70cc)', 'ngk-c7hsa-spark-plug', 'NGK-SPK-C7HSA', 'C7HSA-4629', 'CR7HSA, U22FS-U, 98056-57716', 6, 4, 'Genuine', '100% Original NGK Japan', 'Standard 10mm short reach spark plug engineered for 70cc and 100cc horizontal motorcycle engines.', '<p>The undisputed choice for Honda CD 70, CD 70 Dream, and all Chinese 70cc motorcycles. Deep copper core ensures rapid heat dissipation, eliminating pre-ignition and engine carbon deposits.</p><ul><li>Thread Size: 10mm / Thread Reach: 12.7mm (1/2")</li><li>Hex Size: 16mm (5/8")</li><li>Fitment: Honda CD70, Pridor 100, United 70, Road Prince 70</li></ul>', 380.00, 480.00, 260.00, 150, 25, 0, 1, 0.05, 'NGK C7HSA Spark Plug Price in Pakistan for CD 70', 'Original NGK C7HSA spark plug for Honda CD70 and 70cc bikes. Best fuel efficiency and easy starting.'),

(7, 'Crown Lifan 70cc Complete Cylinder Block & Piston Kit', 'crown-70cc-cylinder-piston-kit', 'CRN-CYL-070', 'CRN-70-CYLKIT', '12100-087-000, CD70-CYL-STD', 2, 1, 'Aftermarket', 'Crown 3-Month Performance Warranty', 'Complete standard bore 47.0mm alloy cylinder barrel with matched piston, rings, gudgeon pin, and circlips.', '<p>Restore full engine compression and eliminate white exhaust smoke with the Crown Lifan 70cc Cylinder Kit. Cast from high-density aluminum alloy with cross-hatched cast-iron sleeve for long-term heat resistance and low oil consumption.</p><ul><li>Bore Size: 47.0mm (Standard STD 72cc)</li><li>Includes: Cylinder Block, Piston, Ring Set, Pin, Circlips, Head Gasket</li><li>Direct replacement for CD70, CD70 Dream, United 70, and Road Prince 70</li><li>Precision machined for smooth piston travel without engine knocking</li></ul>', 4800.00, 5600.00, 3600.00, 22, 4, 1, 1, 2.10, 'Crown 70cc Cylinder Piston Kit Price in Pakistan | PakBikeParts', 'Buy Crown Lifan 70cc cylinder block kit in Pakistan. Complete piston, rings and gaskets set with nationwide COD.'),

(8, 'Atlas Honda CG125 Genuine Front & Rear Brake Shoe Set', 'honda-cg125-brake-shoe-set', 'AHL-BRK-125', '06430-107-000', 'AHL-06430-KCS-920, BRK-SH-CG', 1, 5, 'Genuine', 'Atlas Honda Genuine', 'Pair of asbestos-free high-friction brake shoes with return springs for responsive, squeak-free braking.', '<p>Keep your braking sharp and safe on city roads and highways. Genuine Atlas Honda brake shoes utilize non-asbestos friction lining formulated to prevent drum grooving while delivering instantaneous stopping power in wet or dry weather.</p><ul><li>Part Number: 06430-107-000</li><li>Fits both Front and Rear drums of Honda CG125 and CG125 Special Edition</li><li>Includes 2 heavy-duty tension return springs</li><li>Fade-resistant performance under repeated hard braking</li></ul>', 1250.00, 1500.00, 900.00, 60, 10, 1, 1, 0.35, 'Honda CG 125 Brake Shoes Price in Pakistan | PakBikeParts', '100% Genuine Atlas Honda CG125 brake shoe set. High safety stopping power, no squeak, COD across Pakistan.'),

(9, 'Atlas Honda CD70 Complete Control Cables Kit (4 Cables)', 'honda-cd70-control-cables-kit', 'AHL-CBL-070', 'AHL-CBL-SET-70', '45450-087-000, 22870-087-000', 1, 5, 'Genuine', 'Atlas Honda Genuine', 'Complete set of 4 cables: Clutch cable, Throttle cable, Front brake cable, and Speedometer meter cable.', '<p>Replace worn or stiff cables with this complete 4-piece cable set. Teflon-lined outer sleeves ensure effortless clutch pull, razor-sharp throttle snap-back, and smooth speedometer needle movement.</p><ul><li>Includes: 1x Clutch Cable, 1x Throttle Cable, 1x Front Brake Cable, 1x Speedometer Cable</li><li>Fits all models of Honda CD 70 and Chinese 70cc motorcycles</li><li>Stainless steel inner wire with rust-proof galvanized fittings</li></ul>', 1150.00, 1450.00, 800.00, 40, 8, 0, 1, 0.40, 'Honda CD 70 Control Cables Set Price in Pakistan | PakBikeParts', 'Original Atlas Honda CD70 complete cables set (Clutch, Throttle, Brake, Meter). Best price with cash on delivery.'),

(10, 'Pak Suzuki GD110S Front Hydraulic Shock Absorber Assembly (Pair)', 'suzuki-gd110s-front-shocks-pair', 'SGP-SHK-110', '51100-24B00', 'GD110-FSHK-PR, 51100-33G00', 4, 6, 'Genuine', 'Pak Suzuki Genuine', 'Pair of complete front telescopic hydraulic shock assemblies with chrome stanchions and fork oil.', '<p>Restore plush, bump-free ride quality on your Suzuki GD 110S. These original factory-assembled front fork shocks feature multi-stage oil damping to absorb rough Pakistani potholes and speed breakers without bottoming out.</p><ul><li>Part Number: 51100-24B00</li><li>Direct bolt-on fitment for Suzuki GD110 and GD110S</li><li>Pre-filled with genuine Suzuki fork suspension oil</li><li>Hard-chromed inner tubes prevent oil seal leaks</li></ul>', 8900.00, 10500.00, 7200.00, 12, 2, 1, 1, 4.50, 'Suzuki GD 110S Front Shock Absorbers Price in Pakistan', 'Buy genuine Pak Suzuki GD110S front fork shocks pair in Pakistan. Smooth ride, 100% genuine parts on COD.'),

(11, 'Atlas Honda CG125 Clear Lens Round Headlight Assembly', 'honda-cg125-headlight-assembly', 'AHL-LGT-125', '33100-107-000', 'CG125-HDL-CLR, 33100-KCS-920', 1, 7, 'Genuine', 'Atlas Honda Genuine', 'Complete round headlight with chrome bezel, clear glass lens, reflector housing, and 12V 35W halogen bulb.', '<p>Original factory front headlight unit for Honda CG125. Delivers focused, wide-beam illumination on dark highways without blinding oncoming traffic.</p><ul><li>Part Number: 33100-107-000</li><li>Includes clear heat-treated glass lens (non-yellowing), chrome rim, and 12V halogen bulb</li><li>Direct fit on all Honda CG 125 and CG 125 Special Edition models</li></ul>', 1950.00, 2400.00, 1400.00, 30, 5, 0, 1, 0.75, 'Honda CG 125 Headlight Assembly Price in Pakistan | PakBikeParts', 'Original Atlas Honda CG125 clear lens headlight unit. High-brightness beam with nationwide delivery.'),

(12, 'Crown Complete Wiring Harness for Honda CD70 (Euro II)', 'crown-cd70-wiring-harness', 'CRN-WIR-070', 'CRN-CD70-WIRE', '32100-087-000, CD70-WIR-E2', 2, 4, 'Aftermarket', 'Crown Quality Guarantee', 'Full motorcycle main wiring harness with color-coded copper wires, OEM couplers, and fuse holder.', '<p>End electrical shorts, flickering lights, and battery drain issues with this full replacement wiring loom. Built with 100% pure copper core strands and heavy-duty insulated vinyl wrapping matching original Honda wire color codes.</p><ul><li>Direct plug-and-play fit for Honda CD70 (Euro II models) and Chinese 70cc bikes</li><li>Water-resistant couplers prevent short-circuits during monsoon rains and bike wash</li><li>Includes inline glass fuse holder and pre-crimped earth grounds</li></ul>', 1800.00, 2200.00, 1300.00, 25, 4, 0, 1, 0.60, 'Crown CD 70 Complete Wiring Harness Price in Pakistan', 'Buy Crown Honda CD70 main wiring harness in Pakistan. 100% copper wires, plug and play couplers, COD.'),

(13, 'Panther 2.75-17 Rear Heavy-Duty Tubeless Motorcycle Tyre', 'panther-275-17-rear-motorcycle-tyre', 'PNT-TYR-27517', 'PNT-TREKKER-275', 'SER-275-17, GEN-TYR-125', 8, 9, 'Genuine', '6-Month Panther Warranty', 'Deep tread multi-ply rear tyre engineered for maximum traction on asphalt, rain, and gravel roads.', '<p>The most trusted rear motorcycle tyre size in Pakistan for 125cc motorcycles. Features deep directional tread grooves to channel water away, preventing hydroplaning while offering high puncture resistance against road debris.</p><ul><li>Size: 2.75-17 (Rear)</li><li>Ply Rating: 6 PR Heavy Duty</li><li>Compatible with Honda CG125, Yamaha YB125Z, United 125, Suzuki GS150</li><li>Tubeless ready (can also be used with standard 2.75-17 tube)</li></ul>', 5200.00, 5800.00, 4100.00, 25, 5, 1, 1, 3.20, 'Panther 2.75-17 Motorcycle Tyre Price in Pakistan | PakBikeParts', 'Buy Panther 2.75-17 rear tyre for Honda CG125 & 125cc bikes in Pakistan. Puncture resistant, COD delivery.'),

(14, 'Servis 2.50-17 Front High-Grip Motorcycle Tyre', 'servis-250-17-front-motorcycle-tyre', 'SER-TYR-25017', 'SER-CRUISER-250', 'PNT-250-17, 2.50-17-F', 9, 9, 'Genuine', '6-Month Servis Warranty', 'Precision front steering tyre with straight center rib for agile maneuverability and high stability at speed.', '<p>Pakistan\'s standard front tyre for 70cc, 100cc, and 125cc bikes. The center circumferential groove delivers razor-sharp steering feedback while resisting uneven shoulder wear.</p><ul><li>Size: 2.50-17 (Front)</li><li>Ply Rating: 4 PR</li><li>Direct fitment for Honda CD70, CG125, Pridor 100, and all Chinese bikes</li></ul>', 4100.00, 4600.00, 3200.00, 30, 5, 0, 1, 2.60, 'Servis 2.50-17 Front Motorcycle Tyre Price in Pakistan', 'Original Servis 2.50-17 front tyre for CD70 and CG125. Long-lasting tread compound, nationwide COD.'),

(15, 'Caltex Havoline 4T 20W-50 Motorcycle Engine Oil (0.7 Litre for CD70)', 'caltex-havoline-4t-20w50-0-7l', 'CTX-OIL-07L', 'CTX-HAV-4T-700', 'HAVOLINE-20W50-07, 4T-700ML', 10, 9, 'Genuine', '100% Genuine Caltex Pakistan', 'High-shear stability mineral engine oil formulated for 70cc motorcycles with wet-clutch protection.', '<p>Specially packaged in the exact 0.7 Litre capacity required for Honda CD70 and 70cc bikes to prevent over-filling. Contains Havoline Deposit Shield technology to keep bike engine components clean and prevent clutch slipping.</p><ul><li>Viscosity: SAE 20W-50 / Volume: 0.7 Litre (700ml)</li><li>API SL / JASO MA2 Certified (Highest wet clutch rating)</li><li>Perfect for Pakistani summer temperatures up to 48°C</li><li>Recommended change interval: 1,000 km - 1,200 km</li></ul>', 1050.00, 1200.00, 850.00, 80, 15, 1, 1, 0.70, 'Caltex Havoline 4T 0.7L Engine Oil Price in Pakistan for CD 70', 'Original Caltex Havoline 4T 20W50 700ml oil for CD 70. 100% genuine sealed bottle on cash on delivery.'),

(16, 'Caltex Havoline 4T 20W-50 Premium Motorcycle Oil (1.0 Litre for CG125 / YBR)', 'caltex-havoline-4t-20w50-1-0l', 'CTX-OIL-10L', 'CTX-HAV-4T-1000', 'HAVOLINE-20W50-1L, 4T-1000ML', 10, 9, 'Genuine', '100% Genuine Caltex Pakistan', 'Full 1.0 Litre bottle for 125cc and 150cc motorcycles. Maximum thermal protection against engine overheating.', '<p>Recommended for Honda CG125, CB150F, Yamaha YBR125, and Suzuki GS150. Formulated to withstand the intense thermal stress of air-cooled overhead valve (OHV) engines in congested city traffic.</p><ul><li>Viscosity: SAE 20W-50 / Volume: 1.0 Litre (1000ml)</li><li>API SL / JASO MA2 Performance</li><li>Provides seamless gear shifts and silent tappet operation</li></ul>', 1450.00, 1650.00, 1150.00, 60, 12, 0, 1, 1.00, 'Caltex Havoline 4T 1.0L Engine Oil Price in Pakistan for CG 125 & YBR', 'Buy original Caltex Havoline 4T 1 Litre engine oil for CG125, YBR and GS150 in Pakistan. Fast COD.');

-- 6. BIKE FITMENT MATRIX (product_fitment)
-- Connect parts to compatible bike models
INSERT INTO `product_fitment` (`product_id`, `vehicle_id`, `notes`) VALUES
-- CD70 Carburetor fits CD70, CD70 Dream, United 70, Road Prince 70
(1, 1, 'Direct bolt-on replacement for stock carburetor'),
(1, 2, 'Direct fitment for CD70 Dream'),
(1, 16, 'Compatible with United US 70'),
(1, 18, 'Compatible with Road Prince 70 Passion'),

-- CG125 Clutch Set fits CG125, CG125 Self, CB125F, United 125
(2, 4, 'Full 5-plate set for kick-start CG125'),
(2, 5, 'Compatible with 5-speed CG125 Self/Special'),
(2, 6, 'Compatible with CB125F'),
(2, 17, 'Fits United US 125 engine'),

-- YBR125 Chain Sprocket Kit fits YBR 125, YBR 125G, YB 125Z
(3, 8, 'Standard replacement 14T-43T 428H-118L'),
(3, 9, 'Heavy-duty fitment for YBR 125G trail'),
(3, 10, 'Direct fit for YB 125Z'),
(3, 11, 'Direct fit for YB 125Z-DX'),

-- GS150 CDI Unit fits GS150, GS150 SE
(4, 12, 'Digital advance ignition unit for GS 150'),
(4, 13, 'Fits GS 150 SE'),

-- NGK D8EA Plug fits CG125, CB150F, GS150, YB125Z
(5, 4, 'Recommended standard heat range 8 spark plug'),
(5, 5, 'Standard spark plug for CG125 Self'),
(5, 7, 'Compatible with CB 150F'),
(5, 12, 'Standard plug for Suzuki GS 150'),

-- NGK C7HSA Plug fits CD70, Pridor, United 70, Road Prince 70
(6, 1, 'Standard spark plug for Honda CD70'),
(6, 2, 'Standard spark plug for CD70 Dream'),
(6, 3, 'Compatible with Pridor 100'),
(6, 16, 'Fits United US 70'),
(6, 18, 'Fits Road Prince 70'),

-- Crown 70cc Cylinder fits CD70, CD70 Dream, United 70, Road Prince 70
(7, 1, 'Standard 47.0mm bore STD barrel and piston'),
(7, 2, 'Fits CD70 Dream'),
(7, 16, 'Fits United US 70'),
(7, 18, 'Fits Road Prince 70'),

-- CG125 Brake Shoes fit CG125, CG125 Self
(8, 4, 'Fits both Front & Rear brake drums'),
(8, 5, 'Fits Rear drum on CG125 Self'),

-- CD70 Cable Set fits CD70
(9, 1, 'Complete 4-piece cable set'),
(9, 16, 'Fits United US 70'),

-- GD110S Front Shocks fit GD110S
(10, 14, 'Factory replacement front shock fork pair'),

-- CG125 Headlight fits CG125
(11, 4, 'Round headlight assembly with chrome rim'),
(11, 5, 'Direct fit for CG125 Special Edition'),

-- CD70 Wiring Harness fits CD70
(12, 1, 'Euro II compliant main wiring loom'),
(12, 16, 'Compatible with United US 70 Euro II'),

-- Panther 2.75-17 Rear Tyre fits CG125, YB125Z, GS150
(13, 4, 'Standard rear tyre size for Honda CG 125'),
(13, 10, 'Standard rear tyre size for Yamaha YB 125Z'),
(13, 12, 'Standard rear tyre size for Suzuki GS 150'),

-- Servis 2.50-17 Front Tyre fits CD70, CG125
(14, 1, 'Rear size for CD70 or Front size for CG125'),
(14, 4, 'Standard front tyre for Honda CG 125'),

-- Caltex 0.7L fits CD70
(15, 1, 'Exact 700ml sump capacity for Honda CD 70'),
(15, 2, 'Exact 700ml sump capacity for CD 70 Dream'),

-- Caltex 1.0L fits CG125, YBR, GS150
(16, 4, '1.0L capacity for Honda CG 125'),
(16, 8, '1.0L capacity for Yamaha YBR 125'),
(16, 12, '1.0L capacity for Suzuki GS 150');

-- 7. WHOLESALE MECHANIC TIER PRICING (Bulk discounts for local motorcycle workshops)
INSERT INTO `product_price_tiers` (`product_id`, `min_qty`, `unit_price`) VALUES
-- CD70 Carburetor: Buy 3+ @ Rs. 3,950, Buy 5+ @ Rs. 3,800
(1, 3, 3950.00),
(1, 5, 3800.00),

-- CG125 Clutch Set: Buy 5+ @ Rs. 2,650, Buy 10+ @ Rs. 2,500
(2, 5, 2650.00),
(2, 10, 2500.00),

-- NGK D8EA Plugs: Buy 10+ (Box) @ Rs. 390
(5, 10, 390.00),

-- NGK C7HSA Plugs: Buy 10+ (Box) @ Rs. 330
(6, 10, 330.00),

-- CG125 Brake Shoes: Buy 5+ @ Rs. 1,100, Buy 10+ @ Rs. 1,000
(8, 5, 1100.00),
(8, 10, 1000.00);

-- 8. UPDATE STORE SETTINGS FOR MOTORCYCLE FOCUS
INSERT INTO `settings` (`setting_group`, `setting_key`, `setting_value`) VALUES
('general', 'site_name', 'PakBikeParts'),
('general', 'site_tagline', '100% Genuine & OEM Motorcycle Spare Parts Pakistan'),
('general', 'announcement_banner', '⚡ Free Nationwide Shipping on Motorcycle Parts over Rs. 3,000 | Cash on Delivery (COD) Available'),
('general', 'contact_whatsapp', '923001234567'),
('general', 'contact_phone', '0300-1234567'),
('general', 'contact_email', 'support@pakbikeparts.pk'),
('general', 'contact_address', 'Plaza #28, Auto & Bike Market, Montgomery Road, Lahore, Pakistan')
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);

SET FOREIGN_KEY_CHECKS = 1;

