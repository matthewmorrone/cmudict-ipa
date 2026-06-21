# CMU Dictionary IPA Converter

A modern PHP application for converting between CMU Dictionary format and International Phonetic Alphabet (IPA) notation.

## Features

- Convert CMU Dictionary format to IPA notation
- Reverse conversion from IPA to CMU format
- Multiple output formats (TSV, JSON, XML)
- Batch processing support
- Custom mapping files
- Stress marker handling
- Comprehensive logging
- Unit tested

## Requirements

- PHP 8.1 or higher
- Composer

## Installation

```bash
# Clone the repository
git clone https://github.com/matthewmorrone/cmudict-ipa.git
cd cmudict-ipa

# Install dependencies
composer install

# Make the converter executable
chmod +x bin/convert
```

## Usage

### Basic Usage

```bash
# Convert a file from CMU format to IPA
./bin/convert input.txt output.txt

# Convert using a specific output format
./bin/convert input.txt output.json --format=json

# Convert from IPA back to CMU format
./bin/convert input.txt output.txt --reverse

# Convert without stress markers
./bin/convert input.txt output.txt --no-stress

# Process multiple files
./bin/convert input_directory/ output_directory/ --batch

# Use a custom mapping file
./bin/convert input.txt output.txt --mapping=custom_mapping.tsv
```

### Mapping File Format

The mapping file should be a tab-separated file with two columns:

1. CMU Dictionary phoneme
2. Corresponding IPA symbol

Example:

```
AA	ɑ
AE	æ
AH	ə
```

## Development

### Running Tests

```bash
composer test
```

### Code Style

```bash
# Check code style
composer cs-check

# Fix code style
composer cs-fix
```

### Project Structure

```
.
├── bin/                    # Command-line executables
├── config/                 # Configuration files
├── data/                   # Data files
│   ├── input/             # Input files
│   ├── mappings/          # Mapping files
│   └── output/            # Output files
├── src/                   # Source code
│   ├── Command/          # Console commands
│   ├── Model/            # Data models
│   └── Service/          # Business logic
├── tests/                 # Test files
│   ├── Integration/      # Integration tests
│   └── Unit/             # Unit tests
└── var/                  # Variable files (logs, cache)
    └── logs/             # Log files
```

## Example: Mark Twain's Spelling Reform

Included is Mark Twain's "A Plan for the Improvement of Spelling in the English Language" as an example text that can be processed using this tool.

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Acknowledgments

- CMU Dictionary team for their excellent pronunciation dictionary
- Original cmudict-ipa project contributors
