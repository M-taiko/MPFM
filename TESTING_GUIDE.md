# MPFM System Testing Guide

## 🧪 Quick Start Testing

### Prerequisites
1. ✅ Laravel application running
2. ✅ Database migrated
3. ✅ `test_log.txt` file available
4. ✅ Web server started (Apache/XAMPP)

---

## Test 1: Upload & Preview Workflow

### Steps:
1. **Navigate to Upload Page**
   ```
   http://localhost/MPFM/public/upload
   ```

2. **Fill in Test Details**
   - Client: "Oil Field"
   - Field: "Bab Field"
   - Well: "Zk-89"
   - Operator: "John Doe"
   - Test Date: Today's date
   - Test Type: "Production Test"
   - Wellhead Tower: "Tower 2"
   - Wellhead String: "String A"
   - Choke Size: "32/64"

3. **Upload Log File**
   - Select `test_log.txt`
   - Click "Process Log File"

4. **Expected Result: Preview Page**
   - ✅ File information displayed (size, rows, columns)
   - ✅ Device metadata shown (DAFC version, serial number)
   - ✅ Test summary table
   - ✅ Calculated KPIs (Oil Rate, Gas Rate, Water Rate, GOR, BSW, etc.)
   - ✅ Two charts rendered (Rates Over Time, GOR Over Time)
   - ✅ Data table preview (first 20 rows of 80+ columns)
   - ✅ "Confirm & Save" button
   - ✅ "Cancel & Discard" button

5. **Click "Confirm & Save"**
   - Confirmation dialog appears
   - Click OK
   - Redirected to tests index
   - Success message shown

6. **Verify in Database**
   ```sql
   SELECT * FROM well_tests ORDER BY created_at DESC LIMIT 1;
   ```
   - Check `test_data` JSON contains all rows
   - Check `calculated_metrics` JSON contains KPIs
   - Check `metadata` JSON contains device info
   - Check `log_file_path` points to uploaded file

---

## Test 2: Different File Formats

### Test 2A: Comma-Delimited File
1. Create a CSV file with commas as delimiters
2. Upload through same workflow
3. Verify parser auto-detects comma delimiter
4. Verify preview displays correctly

### Test 2B: File with H2S Columns
1. Use a log file that includes H2S measurements
2. Upload and verify H2S values calculated
3. Check quality report for H2S warnings if negative

### Test 2C: File with Missing Columns
1. Create a log file missing some optional columns
2. Verify system doesn't crash
3. Verify missing columns shown as "N/A"

---

## Test 3: Preview Discard Workflow

### Steps:
1. Upload a test file
2. Review preview page
3. Click "Cancel & Discard"
4. Verify redirected back to upload form
5. Verify temp file deleted from storage
6. Verify nothing saved to database

---

## Test 4: Validation & Error Handling

### Test 4A: Invalid File Type
1. Try uploading a PDF or Word document
2. Expected: Validation error "Invalid file type"

### Test 4B: Empty File
1. Upload an empty text file
2. Expected: Error "No data rows found"

### Test 4C: Corrupted File
1. Upload a file with invalid format
2. Expected: Graceful error message

### Test 4D: Missing Required Fields
1. Leave "Client" field empty
2. Try to submit
3. Expected: Validation error

---

## Test 5: Existing Tests Display

### Steps:
1. Navigate to `/well-tests`
2. Verify saved tests are listed
3. Click "View" on a test
4. Verify test details displayed correctly
5. Verify charts render
6. Verify all data visible

---

## Test 6: Column Detection

### Verify:
1. All 80+ columns from `test_log.txt` detected
2. Column types correctly identified (numeric vs text)
3. Rate columns identified correctly
4. Pressure columns identified correctly
5. Temperature columns identified correctly
6. Water-related columns identified correctly

### Check in Preview:
- Metadata should show:
  - `total_columns: 80+`
  - `delimiter: tab`
  - `column_types: {...}`

---

## Test 7: Calculation Accuracy

### Verify KPIs:
1. **Oil Rate** - Should match average from `qo_sc[STB/d]` or `qo_lc[bbl/d]`
2. **Water Rate** - Should be positive (negative values corrected)
3. **Gas Rate** - Should match average from `qg_sc[MMScf/d]`
4. **GOR** - Should equal Gas Rate / Oil Rate
5. **BSW** - Should be percentage
6. **WLR** - Should be percentage

### Manual Verification:
1. Open `test_log.txt` in Excel/Calc
2. Calculate average of first 10 rows manually
3. Compare with system calculations
4. Should match within rounding error

---

## Test 8: Chart Functionality

### Rates Chart:
1. Should show multiple lines (Oil, Water, Gas)
2. X-axis should show time
3. Y-axis should show rates
4. Lines should be different colors
5. Hover should show tooltips

### GOR Chart:
1. Should show single line for GOR
2. Should follow time series
3. Should be readable and clear

---

## Test 9: Session Management

### Test:
1. Upload a file → See preview
2. Close browser (don't save)
3. Re-open upload page
4. Try to access preview directly
5. Expected: Preview expired or cleared

### Test:
1. Upload file → See preview
2. Click "Confirm & Save"
3. Verify session cleared after save
4. Try to access preview again
5. Expected: No preview data

---

## Test 10: Performance

### Large File Test:
1. Use `test_log.txt` (258 KB, 8000+ rows, 80+ columns)
2. Measure upload time
3. Measure parsing time
4. Measure preview generation time
5. Expected: < 5 seconds total

### Memory Test:
1. Upload large file
2. Monitor PHP memory usage
3. Expected: No memory limit errors

---

## Common Issues & Solutions

### Issue 1: "Temporary file not found"
**Solution**: Check storage permissions
```bash
chmod -R 775 storage/
php artisan storage:link
```

### Issue 2: "Class MPFMPreviewService not found"
**Solution**: Clear cache and regenerate autoloader
```bash
composer dump-autoload
php artisan config:clear
php artisan cache:clear
```

### Issue 3: Charts not displaying
**Solution**: Check browser console for JavaScript errors
- Verify Chart.js CDN loaded
- Check network tab for 404 errors

### Issue 4: "Undefined array key"
**Solution**: This should be FIXED now with dynamic parsing
- If still occurs, check which column is causing it
- Add column pattern to parser

---

## Debugging Tools

### Check Parsed Data:
```php
// Add to controller temporarily
dd($parsedData);
```

### Check Session Data:
```php
// Add to controller temporarily
dd(Session::get('mpfm_preview'));
```

### Check Database:
```sql
SELECT
    id,
    well,
    test_date,
    JSON_LENGTH(test_data->'$.rows') as row_count,
    JSON_LENGTH(test_data->'$.columns') as column_count
FROM well_tests
ORDER BY created_at DESC;
```

### Check Logs:
```bash
tail -f storage/logs/laravel.log
```

---

## Success Criteria

✅ Phase 1 is SUCCESSFUL if:

1. ✅ `test_log.txt` uploads without errors
2. ✅ Preview page displays all data correctly
3. ✅ 80+ columns detected and shown
4. ✅ KPIs calculated correctly
5. ✅ Charts render without errors
6. ✅ "Confirm & Save" stores data successfully
7. ✅ No "undefined array key" errors
8. ✅ Negative water values handled gracefully
9. ✅ Device metadata extracted and displayed
10. ✅ Session management works correctly

---

## Next Phase Testing

After Phase 1 passes:

### Phase 2 Testing:
- Test comparison engine
- Test dynamic charts
- Test role-based dashboard
- Test enhanced UI/UX
- Test performance optimization

---

## Reporting Issues

If you find bugs:

1. **Document the issue**:
   - Steps to reproduce
   - Expected behavior
   - Actual behavior
   - Error messages
   - Screenshots

2. **Check logs**:
   - Laravel log: `storage/logs/laravel.log`
   - Browser console
   - Network tab

3. **Provide context**:
   - File that caused issue
   - Request data
   - Session data

---

## Quick Command Reference

```bash
# Start Laravel server
php artisan serve

# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Check routes
php artisan route:list | grep upload

# Run migrations
php artisan migrate

# Check database
php artisan tinker
>>> App\Models\WellTest::count()
>>> App\Models\WellTest::latest()->first()

# Check services
>>> app(App\Services\MPFMParsingService::class)
>>> app(App\Services\MPFMPreviewService::class)
```

---

**Ready to test? Start with Test 1!** 🚀
