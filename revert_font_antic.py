import os

# Directories to process
directories = [
    'c:\\Users\\Admin\\projekty www\\gravity\\tci',
    'c:\\Users\\Admin\\projekty www\\gravity\\tci\\blog',
    'c:\\Users\\Admin\\projekty www\\gravity\\tci\\css'
]

old_font_name = 'Bodoni Moda'
new_font_name = 'Antic Didone'

# We want to replace Bodoni Moda link with Antic Didone link INCLUDING latin-ext
target_link_fragment = 'family=Bodoni+Moda:opsz,wght@6..96,400..900&subset=latin,latin-ext&display=swap'
# Also catch simpler version if exists
target_link_fragment_simple = 'family=Bodoni+Moda&display=swap'

new_link = 'family=Antic+Didone&subset=latin,latin-ext&display=swap'

def revert_font_in_file(filepath):
    try:
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()
        
        new_content = content
        
        # 1. Replace Font Name in CSS/Styles
        if old_font_name in new_content:
            new_content = new_content.replace(f"'{old_font_name}'", f"'{new_font_name}'")
            new_content = new_content.replace(f'"{old_font_name}"', f'"{new_font_name}"')
        
        # 2. Replace Google Fonts Link
        if 'family=Bodoni+Moda' in new_content:
            if target_link_fragment in new_content:
                new_content = new_content.replace(target_link_fragment, new_link)
            else:
                # Regex or smarter replace for variable params?
                # Let's try to replace the whole query string if possible or just the family part
                import re
                # Replace family=Bodoni+Moda...&display=swap with family=Antic+Didone...&display=swap
                new_content = re.sub(r'family=Bodoni\+Moda[^&]*', 'family=Antic+Didone&subset=latin,latin-ext', new_content)

        if content != new_content:
            with open(filepath, 'w', encoding='utf-8') as f:
                f.write(new_content)
            print(f"Reverted font in {os.path.basename(filepath)}")
            
    except Exception as e:
        print(f"Error processing {filepath}: {e}")

for d in directories:
    if os.path.exists(d):
        for filename in os.listdir(d):
            if filename.endswith(".html") or filename.endswith(".css"):
                revert_font_in_file(os.path.join(d, filename))
