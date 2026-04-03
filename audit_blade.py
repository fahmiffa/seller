import os
import re

root = r'e:\project\pos\web\resources\views'
for path, dirs, files in os.walk(root):
    for f in files:
        if f.endswith('.blade.php'):
            filepath = os.path.join(path, f)
            with open(filepath, 'r', encoding='utf-8') as content:
                txt = content.read()
                txt = re.sub(r'<script.*?>.*?</script>', '', txt, flags=re.DOTALL)
                txt = re.sub(r'{{--.*?--}}', '', txt, flags=re.DOTALL)
                
                tags = ['if', 'forelse', 'auth', 'guest', 'can', 'section', 'verbatim', 'foreach']
                for tag in tags:
                    start = len(re.findall(f'@{tag}\\b', txt))
                    end = len(re.findall(f'@end{tag}\\b', txt))
                    if start != end:
                        print(f'{filepath}: @{tag} mismatch ({start} vs {end})')
