# Import/Export Feature

This document describes how to use the import and export functionality in Grocery Prophet.

## Overview

The Import/Export feature allows you to:
- **Export** your groceries and prices as CSV files
- **Import** groceries and prices from CSV files

## Exporting Data

### Export Groceries

Click the "Export Groceries" button on the Import/Export page. This will download a CSV file containing all your groceries with:
- **Name**: The name of the grocery item
- **Type**: The grocery type (e.g., fruit, dairy, meat)
- **Unit**: The unit of measurement (kg, l, unit, g, mg, ml)

### Export Prices

Click the "Export Prices" button on the Import/Export page. This will download a CSV file containing all your prices with:
- **Grocery Name**: The name of the grocery item
- **Shop**: The shop where the price was recorded
- **Value**: The price value
- **Date**: The date the price was recorded (YYYY-MM-DD format)

## Importing Data

### Import Groceries

1. Go to the Import/Export page
2. Click "Choose File" under "Import Groceries"
3. Select your CSV file
4. Click the "Import" button
5. A success or error message will appear

**CSV Format for Groceries:**
```
Name,Type,Unit
Apple,fruit,kg
Milk,dairy,l
Chicken Breast,meat,kg
```

**Important Notes:**
- The first row must contain the header: `Name,Type,Unit`
- All enum values must match exactly (see reference on Import/Export page)
- Duplicate grocery names will not be imported
- All three columns are required

### Import Prices

1. Go to the Import/Export page
2. Click "Choose File" under "Import Prices"
3. Select your CSV file
4. Click the "Import" button
5. A success or error message will appear

**CSV Format for Prices:**
```
Grocery Name,Shop,Value,Date
Apple,continente,1.99,2026-04-18
Milk,lidl,0.89,2026-04-18
Chicken Breast,pingo_doce,5.49,2026-04-18
```

**Important Notes:**
- The first row must contain the header: `Grocery Name,Shop,Value,Date`
- The grocery must already exist in your database
- All enum values must match exactly (see reference on Import/Export page)
- Date format must be `YYYY-MM-DD`
- Price value must be a positive number
- All four columns are required

## Valid Enum Values

### Grocery Types
```
beverage, pets, rice_pasta_flour, oils_vinegar, baby, bio_healthy,
home_bazar, frozen, canned_goods, fruit, alcohol, personal_care,
dairy, vegetables, cleaning, bakery_pastry, fish, breakfast_coffee,
meal, snacks_sweets, meat, condiments
```

### Units
```
kg, l, unit, g, mg, ml
```

### Shops
```
continente, rei_dos_precos, canario, lidl, pingo_doce, intermarche, mercadona
```

## Sample Files

Sample CSV files are included in the project:
- `sample_groceries.csv` - Example groceries file
- `sample_prices.csv` - Example prices file

## Error Handling

During import, if any rows have errors, they will be displayed with the row number and error message. Some common errors include:

- **Duplicate grocery names** - A grocery with that name already exists
- **Invalid enum values** - The type, unit, or shop value is not recognized
- **Invalid date format** - The date is not in YYYY-MM-DD format
- **Grocery not found** - When importing prices, the referenced grocery doesn't exist
- **Invalid price value** - The price is not a positive number
- **Missing required fields** - A required column is empty

## Tips

1. Always export your data before making bulk changes, so you have a backup
2. Use the reference section on the Import/Export page to see valid enum values
3. Test with the sample files first to understand the format
4. CSV files can be created or edited in Excel, Google Sheets, or any text editor
5. Be careful with duplicate names - they won't be imported to prevent data conflicts
