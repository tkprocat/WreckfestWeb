<div x-data="{
    previewText: '',
    insertColorCode(code) {
        const input = $refs.input;
        const start = input.selectionStart;
        const end = input.selectionEnd;
        const text = input.value;
        const before = text.substring(0, start);
        const after = text.substring(end, text.length);

        input.value = before + code + after;
        input.focus();
        input.selectionStart = input.selectionEnd = start + code.length;

        // Update preview and trigger Livewire update
        this.previewText = input.value;
        input.dispatchEvent(new Event('input'));
    },
    updatePreview() {
        this.previewText = $refs.input?.value || '';
    }
}"
x-init="updatePreview()"
@input="updatePreview()"
class="space-y-2">
    <!-- Color Code Buttons -->
    <div class="flex flex-wrap gap-2 p-3 bg-gray-50 dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700">
        <span class="text-xs font-semibold text-gray-600 dark:text-gray-400 self-center mr-2">Insert Color:</span>

        <button type="button" @click="insertColorCode('^1')"
                class="px-3 py-1.5 rounded text-sm font-bold border-2 transition hover:scale-105"
                style="background-color: #ff0000; color: white; border-color: #cc0000;"
                title="Red (^1)">
            ^1 Red
        </button>

        <button type="button" @click="insertColorCode('^2')"
                class="px-3 py-1.5 rounded text-sm font-bold border-2 transition hover:scale-105"
                style="background-color: #00ff00; color: black; border-color: #00cc00;"
                title="Green (^2)">
            ^2 Green
        </button>

        <button type="button" @click="insertColorCode('^3')"
                class="px-3 py-1.5 rounded text-sm font-bold border-2 transition hover:scale-105"
                style="background-color: #ff8800; color: white; border-color: #cc6600;"
                title="Orange (^3)">
            ^3 Orange
        </button>

        <button type="button" @click="insertColorCode('^4')"
                class="px-3 py-1.5 rounded text-sm font-bold border-2 transition hover:scale-105"
                style="background-color: #0044ff; color: white; border-color: #0033cc;"
                title="Dark Blue (^4)">
            ^4 D.Blue
        </button>

        <button type="button" @click="insertColorCode('^5')"
                class="px-3 py-1.5 rounded text-sm font-bold border-2 transition hover:scale-105"
                style="background-color: #00ccff; color: black; border-color: #00aacc;"
                title="Light Blue (^5)">
            ^5 L.Blue
        </button>

        <button type="button" @click="insertColorCode('^6')"
                class="px-3 py-1.5 rounded text-sm font-bold border-2 transition hover:scale-105"
                style="background-color: #cc00ff; color: white; border-color: #9900cc;"
                title="Purple (^6)">
            ^6 Purple
        </button>

        <button type="button" @click="insertColorCode('^7')"
                class="px-3 py-1.5 rounded text-sm font-bold border-2 transition hover:scale-105"
                style="background-color: #ffffff; color: black; border-color: #cccccc;"
                title="White (^7)">
            ^7 White
        </button>

        <button type="button" @click="insertColorCode('^8')"
                class="px-3 py-1.5 rounded text-sm font-bold border-2 transition hover:scale-105"
                style="background-color: #888888; color: white; border-color: #666666;"
                title="Gray (^8)">
            ^8 Gray
        </button>

        <button type="button" @click="insertColorCode('^9')"
                class="px-3 py-1.5 rounded text-sm font-bold border-2 transition hover:scale-105"
                style="background-color: #000000; color: white; border-color: #333333;"
                title="Black (^9)">
            ^9 Black
        </button>
    </div>

    <!-- Input Field -->
    <div>
        {{ $slot }}
    </div>

    <!-- Preview -->
    @if($showPreview ?? true)
    <div class="p-3 bg-gray-900 rounded-lg border border-gray-700"
         x-data="{
             formatColorCodes(text) {
                 if (!text) return '';

                 const colors = {
                     '1': '#ff0000',
                     '2': '#00ff00',
                     '3': '#ff8800',
                     '4': '#0044ff',
                     '5': '#00ccff',
                     '6': '#cc00ff',
                     '7': '#ffffff',
                     '8': '#888888',
                     '9': '#000000'
                 };

                 // Split by color codes while keeping them
                 const parts = text.split(/(\^\d)/);
                 let result = '';
                 let currentColor = null;

                 for (let i = 0; i < parts.length; i++) {
                     const part = parts[i];
                     const colorMatch = part.match(/\^(\d)/);

                     if (colorMatch) {
                         // Close previous color if any
                         if (currentColor !== null) {
                             result += '</span>';
                         }
                         // Set new color
                         currentColor = colorMatch[1];
                         if (colors[currentColor]) {
                             result += `<span style='color: ${colors[currentColor]}'>`;
                         }
                     } else if (part) {
                         // Regular text
                         result += part;
                     }
                 }

                 // Close final span if any
                 if (currentColor !== null) {
                     result += '</span>';
                 }

                 return result;
             }
         }">
        <div class="text-xs font-semibold text-gray-400 mb-2">Preview:</div>
        <div class="font-bold text-lg"
             x-html="formatColorCodes(previewText)">
        </div>
    </div>
    @endif

    <div class="text-xs text-gray-500 dark:text-gray-400">
        <strong>Tip:</strong> Click a color button to insert the color code at your cursor position. Text after a color code will appear in that color.
    </div>
</div>
