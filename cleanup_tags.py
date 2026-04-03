import os
import re

path = r'e:\project\pos\web\resources\views'
for root, dirs, files in os.walk(path):
    for file in files:
        if file.endswith('.blade.php'):
            filepath = os.path.join(root, file)
            with open(filepath, 'r', encoding='utf-8') as f:
                content = f.read()
            
            # Simple cleanup: if file ends with </x-app-layout> and then more whitespace and another </x-app-layout>
            # OR if it just has multiple </x-app-layout> in a row
            new_content = content.replace('</x-app-layout>\n</x-app-layout>', '</x-app-layout>')
            new_content = new_content.replace('</x-app-layout></x-app-layout>', '</x-app-layout>')
            
            if new_content != content:
                print(f"Cleaning up {filepath}")
                with open(filepath, 'w', encoding='utf-8') as f:
                    f.write(new_content)
