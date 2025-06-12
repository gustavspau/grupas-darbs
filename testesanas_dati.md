# Noliktavas Vadības Sistēmas Testēšanas Dati

## 1. Lietotāju Testi

### 1.1 Administratora konts
- **Lietotājvārds:** admin
- **Parole:** admin123
- **Loma:** Administrators
- **E-pasts:** admin@noliktava.lv
- **Pilns vārds:** Jānis Bērziņš

### 1.2 Noliktavas darbinieka konts
- **Lietotājvārds:** warehouse1
- **Parole:** warehouse123
- **Loma:** Noliktavas darbinieks
- **E-pasts:** warehouse@noliktava.lv
- **Pilns vārds:** Anna Kalniņa

### 1.3 Plauktu kārtotāja konts
- **Lietotājvārds:** shelf1
- **Parole:** shelf123
- **Loma:** Plauktu kārtotājs
- **E-pasts:** shelf@noliktava.lv
- **Pilns vārds:** Pēteris Ozoliņš

## 2. Produktu Testēšanas Dati

### 2.1 Bulkīšu izstrādājumi
| Produkta kods | Nosaukums | Svītrkods | Vienības cena | Min. krājums | Apraksts |
|---------------|-----------|-----------|---------------|--------------|----------|
| PRD001 | Maize baltā | 1234567890123 | 1.50€ | 20 | Svaiga baltmaize |
| PRD002 | Maize pilngraudu | 1234567890124 | 1.80€ | 15 | Veselīga pilngraudu maize |
| PRD003 | Kūkas šokolādes | 1234567890125 | 12.50€ | 5 | Mājas gatavota šokolādes kūka |

### 2.2 Šķidrums
| Produkta kods | Nosaukums | Svītrkods | Vienības cena | Min. krājums | Apraksts |
|---------------|-----------|-----------|---------------|--------------|----------|
| PRD004 | Ūdens 1.5L | 1234567890126 | 0.89€ | 50 | Dabīgs avota ūdens |
| PRD005 | Sula ābolu 1L | 1234567890127 | 2.50€ | 25 | 100% dabīga ābolu sula |
| PRD006 | Kafija melna | 1234567890128 | 1.20€ | 30 | Aromātiska kafija |

### 2.3 Piena produkti
| Produkta kods | Nosaukums | Svītrkods | Vienības cena | Min. krājums | Apraksts |
|---------------|-----------|-----------|---------------|--------------|----------|
| PRD007 | Piens 2.5% 1L | 1234567890129 | 1.45€ | 40 | Svaigs govs piens |
| PRD008 | Jogurts dabīgais | 1234567890130 | 0.95€ | 35 | Bez piedevām |
| PRD009 | Siers Gouda | 1234567890131 | 8.90€ | 15 | Holandiešu siers |

### 2.4 Dārzeņi
| Produkta kods | Nosaukums | Svītrkods | Vienības cena | Min. krājums | Apraksts |
|---------------|-----------|-----------|---------------|--------------|----------|
| PRD010 | Burkāni 1kg | 1234567890132 | 1.20€ | 25 | Svaigi vietējie burkāni |
| PRD011 | Tomāti kg | 1234567890133 | 3.50€ | 20 | Siltumnīcas tomāti |
| PRD012 | Kartupeļi 2kg | 1234567890134 | 2.10€ | 30 | Vietējie kartupeļi |

### 2.5 Augļi
| Produkta kods | Nosaukums | Svītrkods | Vienības cena | Min. krājums | Apraksts |
|---------------|-----------|-----------|---------------|--------------|----------|
| PRD013 | Āboli kg | 1234567890135 | 2.80€ | 25 | Svaigi rudens āboli |
| PRD014 | Banāni kg | 1234567890136 | 1.95€ | 30 | Gatavi lietošanai |
| PRD015 | Apelsīni kg | 1234567890137 | 3.20€ | 20 | Saldos Spānijas apelsīni |

## 3. Testēšanas Scenāriji

### 3.1 Produkta Pievienošanas Tests
**Pozitīvs tests:**
1. Ielogojies kā administrators
2. Atver "Pievienot produktu" modallogu
3. Aizpildi:
   - Produkta kods: PRD016
   - Nosaukums: Šokolāde tumšā
   - Kategorija: Saldumi
   - Svītrkods: 1234567890138
   - Vienības cena: 4.50
   - Min. krājums: 15
4. Spied "Saglabāt"
5. **Sagaidāmais rezultāts:** Produkts veiksmīgi pievienots

**Negatīvs tests:**
1. Atstāj tukšu produkta nosaukumu
2. **Sagaidāmais rezultāts:** Parādās kļūdas ziņojums "Produkta nosaukums ir obligāts"

### 3.2 Lietotāju Pārvaldības Tests
**Jauna lietotāja pievienošana:**
1. Ielogojies kā administrators
2. Ej uz "Lietotāji" sadaļu
3. Spied "Pievienot lietotāju"
4. Aizpildi:
   - Vārds: Zane
   - Uzvārds: Liepa
   - E-pasts: zane.liepa@test.lv
   - Parole: test123
   - Loma: Noliktavas darbinieks
5. **Sagaidāmais rezultāts:** Lietotājs veiksmīgi pievienots

### 3.3 Krājumu Pārvaldības Tests
**Krājumu atjaunošana:**
1. Ielogojies kā noliktavas darbinieks
2. Atrod produktu ar zemu krājumu
3. Spied uz "Atjaunot krājumus"
4. Ievadi jauno daudzumu
5. **Sagaidāmais rezultāts:** Krājumi atjaunoti

## 4. Validācijas Testi

### 4.1 Produkta Koda Validācija
| Ievades dati | Sagaidāmais rezultāts |
|--------------|----------------------|
| PRD123 | ✅ Derīgs |
| PRD | ❌ Pārāk īss |
| ABC123 | ❌ Jāsākas ar PRD |
| PRD12A | ❌ Pēdējie 3 simboli jābūt cipariem |
| PRD1234 | ❌ Pārāk garš |

### 4.2 Svītrkoda Validācija
| Ievades dati | Sagaidāmais rezultāts |
|--------------|----------------------|
| 1234567890123 | ✅ Derīgs (13 cipari) |
| 123456789012 | ❌ Pārāk īss (12 cipari) |
| 12345678901234 | ❌ Pārāk garš (14 cipari) |
| 123456789012A | ❌ Nesatur tikai ciparus |

### 4.3 Cenas Validācija
| Ievades dati | Sagaidāmais rezultāts |
|--------------|----------------------|
| 10.50 | ✅ Derīgs |
| -5.00 | ❌ Nedrīkst būt negatīva |
| 999999.99 | ✅ Maksimālā atļautā |
| 1000000.00 | ❌ Pārsniedz maksimumu |
| abc | ❌ Nav skaitlis |

## 5. Lomu Piekļuves Testi

### 5.1 Administratora Piekļuve
- ✅ Var pievienot/rediģēt/dzēst produktus
- ✅ Var pārvaldīt lietotājus
- ✅ Var skatīt visus pārskatus
- ✅ Var mainīt sistēmas iestatījumus

### 5.2 Noliktavas Darbinieka Piekļuve
- ✅ Var pievienot/rediģēt produktus
- ✅ Var atjaunot krājumus
- ✅ Var skenēt produktus
- ❌ Nevar pārvaldīt lietotājus

### 5.3 Plauktu Kārtotāja Piekļuve
- ✅ Var skatīt produktus
- ✅ Var skenēt produktus
- ✅ Var organizēt plauktus
- ❌ Nevar pievienot jaunus produktus
- ❌ Nevar pārvaldīt lietotājus

## 6. Sistēmas Veiktspējas Testi

### 6.1 Ielādes Laiks
- Galvenā lapa: < 2 sekundes
- Produktu saraksts: < 3 sekundes
- Pārskata ģenerēšana: < 5 sekundes

### 6.2 Datu Apjoms
- Sistēma var apstrādāt līdz 10,000 produktiem
- Vienlaicīgi var strādāt līdz 50 lietotāji
- Datubāzes rezerves kopijas katru dienu

## 7. Drošības Testi

### 7.1 Autentifikācijas Tests
- Nepareizs lietotājvārds/parole: Pieeja liegta
- Sesijas beigas pēc neaktivitātes: Automātiska atteikšanās
- Vairākkārtēji neveiksmīgi mēģinājumi: Konta bloķēšana

### 7.2 Autorizācijas Tests
- Plauktu kārtotājs mēģina piekļūt admin funkcijai: Piekļuve liegta
- URL manipulācijas: Neautorizēta piekļuve novērsta
- Sesijas pārņemšana: Aizsardzība ieviesta

## 8. Kļūdu Apstrādes Testi

### 8.1 Datubāzes Savienojuma Kļūdas
- Datubāze nepieejama: Lietotājam rāda piemērotu ziņojumu
- Lēna datubāze: Rāda ielādes indikātoru

### 8.2 Tīkla Kļūdas
- Nav interneta savienojuma: Offline režīms
- Lēns savienojums: Optimizētā ielāde

## 9. Lietojamības Testi

### 9.1 Navigācijas Tests
- Visi pogas un saites strādā
- Breadcrumb navigācija
- Meklēšanas funkcionalitāte

### 9.2 Responsīvā Dizaina Tests
- Desktop (1920x1080): Pilna funkcionalitāte
- Tablet (768x1024): Adaptētā saskarne
- Mobile (375x667): Mobilajai ierīcei optimizēts

## 10. Integrācijas Testi

### 10.1 Eksports/Imports
- Excel eksports: Dati pareizi formatēti
- PDF pārskati: Kvalitāte un satura precizitāte
- CSV imports: Datu validācija un apstrāde

### 10.2 Svītrkodu Skenēšana
- Kameras piekļuve: Atļaujas prasīšana
- Svītrkoda atpazīšana: 95%+ precizitāte
- Nepareiza svītrkoda apstrāde: Kļūdas ziņojums 