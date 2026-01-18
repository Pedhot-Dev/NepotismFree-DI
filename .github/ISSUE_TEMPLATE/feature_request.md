name: Feature Request
description: Propose a new feature for the container.
labels: [enhancement]
body:
  - type: markdown
    attributes:
      value: |
        **WARNING**: This project prioritizes architectural integrity and strictness over developer convenience. Features that introduce "magic", service location, or ambiguity will be REJECTED.
  - type: textarea
    attributes:
      label: Proposed Feature
      description: Describe the feature clearly.
    validations:
      required: true
  - type: textarea
    attributes:
      label: Architectural Justification
      description: Why does this feature belong in a strict, deterministic container?
    validations:
      required: true
  - type: dropdown
    attributes:
      label: Determinism Check
      description: Does this feature introduce any non-deterministic or "magical" behavior?
      options:
        - "No, it is fully explicit and deterministic."
        - "It might add some convenience at the cost of strictness (EXPECT REJECTION)."
    validations:
      required: true
