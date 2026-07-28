import pandas as pd
import json
import os
import math

def clean_building_name(name):
    if not isinstance(name, str):
        return ""
    # Standardize names to match the PHP list as much as possible
    name = name.strip().lower()
    
    # Common mappings derived from analysis
    if "gadhen" in name: return "Gadhen Khang"
    if "phende" in name: return "Phende Khang"
    if "delek delekyi" in name: return "Delek Delekyi Khang"
    if "tashi" in name: return "Tashi Khang"
    if "phuntsok" in name: return "Phuntsok Khang"
    if "songtsen" in name: return "Songtsen Khang"
    if "offical" in name or "official" in name: return "Official"
    if "kalon quat" in name: return "Kalon Quat"
    if "moenkey" in name: return "Moenkey Khang"
    if "namgyal" in name: return "Namgyal Khang"
    if "kalsang" in name: return "Kalsang Khang"
    if "shindhey" in name: return "Shindhey Khang"
    if "health" in name: return "Health leshakey"
    if "sunney" in name: return "Shering nyiwoe Khang"
    if "shering nyiwoe" in name: return "Shering nyiwoe Khang"
    if "mcleod" in name: return "Nelen Khang McLeod"
    if "nelen" in name: return "Nelen Khang McLeod"
    if "lemonk" in name: return "Lemonk Khang"
    
    return name.title()

def main():
    file_path = os.path.join(os.path.dirname(__file__), '..', 'data.xlsx')
    if not os.path.exists(file_path):
        print("data.xlsx not found")
        return

    # Read All Sheets (though analysis showed only Sheet1 matters)
    xl = pd.ExcelFile(file_path)
    
    all_consumers = []
    
    building_names_to_search = [
        "Gadhen Khang", "Phende Khang", "Delek Delekyi Khang", "Tashi Khang",
        "Desung Leyshak", "Phuntsok Khang", "Songtsen Khang", "Health leshakey",
        "Shindhey Khang", "Thogmoen Khang", "Moenkey Khang", "Sonam Khang",
        "Shering nyiwoe Khang", "Sunney Hostel", "Namsey Khang", "Dekyi Khang", "Back of Education",
        "Rabten Khang", "Lemonk Khang", "Kalsang Khang", "Phelgyal Khang",
        "Pelbar Khang", "Nangsi Leyshak", "Namgyal Khang", "Shiney Khang",
        "Kalon Quat", "Official", "Offical", "Nelen Khang McLeod", "Nelen Khang Mecloed"
    ]

    for sheet_name in xl.sheet_names:
        df = pd.read_excel(file_path, sheet_name=sheet_name, header=None)
        rows, cols = df.shape
        
        # Scan for building starts
        for r in range(rows - 2): # Need at least 2 more rows for header and data
            for c in range(cols):
                val = str(df.iloc[r, c]).lower()
                
                # Check if this cell is a building name
                is_building = False
                matched_name = ""
                for b in building_names_to_search:
                    if b.lower() in val:
                        is_building = True
                        matched_name = clean_building_name(b)
                        break
                
                if is_building:
                    # Check if the row below contains "S.no" or "Account" or similar header indicator
                    header_row_val = str(df.iloc[r+1, c]).lower()
                    if "s.no" in header_row_val or "account" in header_row_val:
                        print(f"Found Block: {matched_name} at Row {r}, Col {c}")
                        
                        # Process block
                        header_r = r + 1
                        data_start_r = r + 2
                        
                        # Identify columns in this block
                        cid_col = -1
                        name_col = -1
                        meter_col = -1
                        inid_col = -1
                        acc_col = -1
                        dept_col = -1
                        phase_col = -1
                        inst_col = -1
                        
                        # Look at headers in row r+1
                        header_cells = [str(df.iloc[header_r, c + offset]).lower() for offset in range(12) if c + offset < cols]
                        
                        # Priority list for CID
                        cid_keywords = ["consurmer id", "consumer id", "con id no"]
                        account_keywords = ["a/c no", "account no", "ac no"]
                        meter_keywords = ["meter no", "meter number"]
                        inid_keywords = ["in id"]
                        dept_keywords = ["department"]
                        phase_keywords = ["type of meter"]
                        inst_keywords = ["installtion no"]
                        
                        for idx, h in enumerate(header_cells):
                            if any(k in h for k in cid_keywords):
                                cid_col = c + idx
                                break # Found best match
                            elif cid_col == -1 and any(k in h for k in account_keywords):
                                cid_col = c + idx
                        
                        for idx, h in enumerate(header_cells):
                            if "name" in h or "payment by" in h:
                                name_col = c + idx
                                break

                        for idx, h in enumerate(header_cells):
                            if any(k in h for k in meter_keywords):
                                meter_col = c + idx
                                break

                        for idx, h in enumerate(header_cells):
                            if any(k in h for k in inid_keywords):
                                inid_col = c + idx
                                break

                        for idx, h in enumerate(header_cells):
                            if any(k in h for k in account_keywords) and (c + idx) != cid_col:
                                acc_col = c + idx
                                break

                        for idx, h in enumerate(header_cells):
                            if any(k in h for k in dept_keywords):
                                dept_col = c + idx
                                break

                        for idx, h in enumerate(header_cells):
                            if any(k in h for k in phase_keywords):
                                phase_col = c + idx
                                break

                        for idx, h in enumerate(header_cells):
                            if any(k in h for k in inst_keywords):
                                inst_col = c + idx
                                break
                        
                        if cid_col == -1:
                            print(f"  Warning: No CID column found for {matched_name}")
                            continue
                            
                        # Extract data rows
                        for dr in range(data_start_r, rows):
                            room_val = df.iloc[dr, c]
                            
                            # Break if room is empty or looks like another building name
                            if pd.isna(room_val): break
                            
                            # Robust Room check
                            try:
                                room_str = str(int(float(room_val)))
                            except:
                                # Might be text like "Total"
                                break
                                
                            cid_val = df.iloc[dr, cid_col]
                            name_val = df.iloc[dr, name_col] if name_col != -1 else ""
                            meter_val = df.iloc[dr, meter_col] if meter_col != -1 else ""
                            inid_val = df.iloc[dr, inid_col] if inid_col != -1 else ""
                            acc_val = df.iloc[dr, acc_col] if acc_col != -1 else ""
                            dept_val = df.iloc[dr, dept_col] if dept_col != -1 else ""
                            phase_val = df.iloc[dr, phase_col] if phase_col != -1 else ""
                            inst_val = df.iloc[dr, inst_col] if inst_col != -1 else ""
                            
                            if pd.isna(cid_val): cid_val = ""
                            if pd.isna(name_val): name_val = ""
                            if pd.isna(meter_val): meter_val = ""
                            if pd.isna(inid_val): inid_val = ""
                            if pd.isna(acc_val): acc_val = ""
                            if pd.isna(dept_val): dept_val = ""
                            if pd.isna(phase_val): phase_val = ""
                            if pd.isna(inst_val): inst_val = ""
                            
                            # Cleanup
                            cid_val = str(cid_val).split('.')[0].strip() # Handle float-like IDs
                            name_val = str(name_val).strip()
                            meter_val = str(meter_val).split('.')[0].strip() # Handle float-like Meter IDs
                            inid_val = str(inid_val).split('.')[0].strip()
                            acc_val = str(acc_val).split('.')[0].strip()
                            dept_val = str(dept_val).strip()
                            phase_val = str(phase_val).strip()
                            inst_val = str(inst_val).split('.')[0].strip()
                            
                            if cid_val and cid_val.lower() != 'nan':
                                all_consumers.append({
                                    'building': matched_name,
                                    'room': room_str,
                                    'name': name_val,
                                    'cid': cid_val,
                                    'meter': meter_val,
                                    'inid': inid_val,
                                    'account': acc_val,
                                    'department': dept_val,
                                    'phase': phase_val,
                                    'installation': inst_val
                                })
                                
    print(f"Total Consumers Extracted: {len(all_consumers)}")
    
    # Deduplicate (blocks might overlap or be repeated)
    unique_consumers = []
    seen = set()
    for con in all_consumers:
        key = (con['building'], con['room'], con['cid'])
        if key not in seen:
            unique_consumers.append(con)
            seen.add(key)
            
    print(f"Unique Consumers: {len(unique_consumers)}")

    # Save to JSON
    json_path = os.path.join(os.path.dirname(__file__), '..', 'consumers.json')
    with open(json_path, 'w') as f:
        json.dump(unique_consumers, f, indent=4)
        print(f"Saved to {json_path}")

if __name__ == "__main__":
    main()
