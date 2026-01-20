import os

# Directories to process
directories = [
    'c:\\Users\\Admin\\projekty www\\gravity\\tci',
    'c:\\Users\\Admin\\projekty www\\gravity\\tci\\blog',
    'c:\\Users\\Admin\\projekty www\\gravity\\tci\\css'
]

old_font_name = 'Antic Didone'
new_font_name = 'Bodoni Moda'

# Regex/Exact string for Link replacement
# Old link matches broadly: family=Antic+Didone...
# We will read file content and use string replace for simplest robust match if exact.

# The old link often has subset parameters now.
# We will search for 'family=Antic+Didone' and replace line or segment?
# Safest is to replace 'family=Antic+Didone&subset=latin,latin-ext&display=swap' AND 'family=Antic+Didone&display=swap'
# With 'family=Bodoni+Moda:opsz,wght@6..96,400..900&subset=latin,latin-ext&display=swap'

new_font_link_part = 'family=Bodoni+Moda:opsz,wght@6..96,400..900&subset=latin,latin-ext&display=swap'

def replace_font_in_file(filepath):
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        new_content = content
        
        # 1. Replace Font Name in CSS/Styles
        if old_font_name in new_content:
            new_content = new_content.replace(f"'{old_font_name}'", f"'{new_font_name}'")
            new_content = new_content.replace(f'"{old_font_name}"', f'"{new_font_name}"')
        
        # 2. Replace Google Fonts Link
        # Catch various previous versions of the link
        if 'family=Antic+Didone' in new_content:
            # We assume standard GFonts URL structure, replacing just the query part effectively
            # But simpler to just replace the whole family param if possible.
            # Let's replace simple specific known strings first.
            
            # Case 1: With subset
            new_content = new_content.replace('family=Antic+Didone&subset=latin,latin-ext&display=swap', new_font_link_part)
            # Case 2: Without subset
            new_content = new_content.replace('family=Antic+Didone&display=swap', new_font_link_part)
            # Case 3: Just param if mixed (less safe but fallback)
            # new_content = new_content.replace('family=Antic+Didone', 'family=Bodoni+Moda:opsz,wght@6..96,400..900')

        if content != new_content:
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(new_content)
            print(f"Updated font in {os.path.basename(filepath)}")
            
    except Exception as e:
        print(f"Error processing {filepath}: {e}")

for d in directories:
    if os.path.exists(d):
        for filename in os.listdir(d):
            if filename.endswith(".html") or filename.endswith(".css"):
                replace_font_in_file(os.path.join(d, filename))
