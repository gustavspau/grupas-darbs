# Noliktavas Vadības Sistēma
## Warehouse Management System
Moderna noliktavas vadības sistēma ar dažādām lietotāju lomām.
## 🚀 Iestatīšana (Setup)
### 1. Datubāzes iestatīšana
1. Atveriet phpMyAdmin savā XAMPP izvēlnē
2. Izveidojiet jaunu datubāzi vai izmantojiet esošo
3. Importējiet `warehouse_management.sql` failu:
   - Noklikšķiniet uz "Import" tab
   - Izvēlieties `warehouse_management.sql` failu
   - Noklikšķiniet "Go"
### 2. Web servera iestatīšana
1. Pārliecinieties, ka XAMPP Apache serveris darbojas
2. Ievietojiet projekta failus `C:\xampp\htdocs\grupas-darbs\` direktorijā
3. Atveriet pārlūkprogrammā: `http://localhost/grupas-darbs/`
## 👥 Lietotāju lomas (User Roles)
### Administrator
- **Lietotājvārds:** admin
- **Parole:** password
- **Funkcijas:** 
  - Lietotāju pārvaldība
  - Sistēmas iestatījumi
  - Atskaites
  - Inventāra pārvaldība
### Noliktavas darbinieks
- **Lietotājvārds:** janis.berzins
- **Parole:** password
- **Funkcijas:**
  - Preču pieņemšana
  - Preču nosūtīšana
  - Inventāra uzskaite
  - Uzdevumu veikšana
### Plauktu kartotājs
- **Lietotājvārds:** anna.ozolina
- **Parole:** password
- **Funkcijas:**
  - Plauktu organizēšana
  - Papildināšana
  - Plauktu pārbaude
  - Apkope
## 📊 Funkcionalitāte
### Galvenās funkcijas:
- **Lietotāju autentifikācija** ar lomām
- **Interaktīva noliktavas karte** ar krāsu kodēšanu
- **Inventāra vadība** ar svītrkodu atbalstu
- **Uzdevumu pārvaldība** ar prioritātēm
- **Reāllaika statistika** un atskaites
- **Aktivitāšu žurnāls** visām darbībām
### Tehnoloģijas:
- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **Backend:** MySQL datubāze
- **Stils:** Modern responsive design
- **Ikonas:** Font Awesome 6
- **Valoda:** Latviešu
## 📁 Failu struktūra
```
grupas-darbs/
├── index.html              # Galvenā HTML lapa
├── styles.css              # CSS stili
├── script.js               # JavaScript funkcionalitāte
├── warehouse_management.sql # Datubāzes shēma
└── README.md               # Dokumentācija
```
## 🗄️ Datubāzes struktūra
### Galvenās tabulas:
- `users` - Lietotāju informācija
- `warehouse_sections` - Noliktavas sekcijas  
- `shelves` - Plauktu informācija
- `products` - Produktu katalogs
- `inventory` - Inventāra uzskaite
- `orders` - Pasūtījumi (ienākošie/izejošie)
- `tasks` - Uzdevumi darbiniekiem
- `activity_log` - Darbību žurnāls
### Papildu funkcijas:
- **Views:** Produktu atlikumi, plauktu noslodze, aktīvie uzdevumi
- **Triggers:** Automātiska statistikas atjaunināšana
- **Procedures:** Inventāra kustību reģistrēšana
- **Functions:** Atlikumu aprēķini
## 🎨 Lietotāja saskarnes
### Dizaina īpašības:
- Modern gradient dizains
- Responsive layout (mobilās ierīces)
- Intuitīva navigācija
- Krāsu kodēti statusi
- Reāllaika atjauninājumi
- Animācijas un pārejas
### Plauktu statusi:
- 🟢 **Normāls** - Viss kārtībā
- 🟡 **Zems atlikums** - Nepieciešama papildināšana  
- 🔴 **Organizēšana** - Nepieciešama kārtošana
- 🔵 **Apkope** - Tehniska apkope
## 📱 Atbalstītās funkcijas
- ✅ Role-based access control
- ✅ Barcode scanning simulation
- ✅ Interactive warehouse map
- ✅ Real-time updates
- ✅ Task management
- ✅ Inventory tracking
- ✅ Order processing
- ✅ Activity logging
- ✅ Responsive design
- ✅ Latvian localization
## 🔧 Problēmu risināšana
### Biežākās problēmas:
1. **Nevar pieslēgties datubāzei**
   - Pārbaudiet XAMPP MySQL statusu
   - Pārbaudiet datubāzes nosaukumu
2. **Lapas neielādējas**
   - Pārbaudiet Apache servera statusu
   - Pārbaudiet failu ceļus
3. **JavaScript kļūdas**
   - Atveriet Developer Tools (F12)
   - Pārbaudiet Console tab
## 📞 Atbalsts
Ja rodas problēmas, pārbaudiet:
1. XAMPP kontroles paneli
2. Browser Developer Tools
3. MySQL error logs
4. PHP error logs
---
**Versija:** 1.0  
**Izveidots:** 2024  
**Valoda:** Latviešu  
**Licences:** Open Source
