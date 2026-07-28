import json
import os

def main():
    json_path = os.path.join(os.path.dirname(__file__), '..', 'consumers.json')
    
    if not os.path.exists(json_path):
        print("Error: consumers.json not found. Run convert_data.py first.")
        return
        
    with open(json_path, 'r') as f:
        consumers = json.load(f)
        
    print(f"Loaded {len(consumers)} records from consumers.json.")
    
    # Load complaints if they exist (mocking or connecting to DB/storage if applicable)
    # Since this is a legacy script, we'll assume it's for local data inspection
    
    while True:
        term = input("\nEnter search term (Building, Room, Name, Phone, or CID): ").strip().lower()
        if not term: break
        
        matches = []
        for c in consumers:
            # Search across all consumer fields
            if (term in c['building'].lower() or 
                term in str(c['room']).lower() or 
                term in c['name'].lower() or 
                term in str(c.get('phone', '')).lower() or
                term in str(c['cid']).lower()):
                matches.append(c)
                
        print(f"\n--- Records Found ({len(matches)}) ---")
        for m in matches:
            print(f"Building: {m['building']}")
            print(f"Room Number: {m['room']}")
            print(f"Name: {m['name']}")
            print(f"Consumer ID: {m['cid']}")
            if 'phone' in m: print(f"Phone: {m['phone']}")
            
            # Here we "track status" conceptually - in this legacy script 
            # we can show where they stand in the system.
            print("Status: Active Consumer")
            print("-" * 20)

if __name__ == "__main__":
    main()
