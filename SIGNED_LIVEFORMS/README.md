# SIGNED Live Forms

## Instructions

Please copy your SIGNED_LIVEFORMS folder contents here.

### Required Forms:

1. **BEEF (Basic Education Enrollment Form)**
   - File: `BEEF.pdf` or `BEEF.docx`
   - Extract all fillable fields
   - Map to database columns

2. **IEP Part 1 (Individualized Education Plan)**
   - File: `IEP_P1.pdf` or `IEP_P1.docx`
   - Extract all fillable fields
   - Note: Part B is optional but should be present

3. **Enrollment List**
   - File: `ENROLLMENT_LIST.pdf` or similar
   - Format for displaying enrolled students

### Next Steps:

After copying the forms here:
1. Review each form's fillable fields
2. Create database mapping document
3. Build web forms based on the PDF/DOCX fields
4. Implement auto-fill logic where possible

### Form Field Mapping:

Create a file `form_fields_mapping.json` with structure:
```json
{
  "BEEF": {
    "fields": [
      {"name": "learner_name", "type": "text", "required": true},
      {"name": "date_of_birth", "type": "date", "required": true}
    ]
  },
  "IEP_P1": {
    "fields": [
      {"name": "present_level", "type": "textarea", "required": true}
    ]
  }
}
```

## Current Status:
- [ ] Copy BEEF form
- [ ] Copy IEP P1 form
- [ ] Copy Enrollment List format
- [ ] Create field mapping document
- [ ] Extract all fillable fields
